<?php

declare(strict_types=1);

namespace XbNz\Fping;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Webmozart\Assert\Assert;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\DTOs\PingResultDTO;
use XbNz\Fping\ValueObjects\Sequence;
use XbNz\Shared\BinFinder;
use XbNz\Shared\IpValidator;

use function Psl\Filesystem\canonicalize;

/**
 * Usage: fping [options] [targets...]
 *
 * Probing options:
 * -4, --ipv4         only ping IPv4 addresses
 * -6, --ipv6         only ping IPv6 addresses
 * -b, --size=BYTES   amount of ping data to send, in bytes (default: 56)
 * -B, --backoff=N    set exponential backoff factor to N (default: 1.5)
 * -c, --count=N      count mode: send N pings to each target and report stats
 * -f, --file=FILE    read list of targets from a file ( - means stdin)
 * -g, --generate     generate target list (only if no -f specified)
 * (give start and end IP in the target list, or a CIDR address)
 * (ex. fping -g 192.168.1.0 192.168.1.255 or fping -g 192.168.1.0/24)
 * -H, --ttl=N        set the IP TTL value (Time To Live hops)
 * -i, --interval=MSEC  interval between sending ping packets (default: 10 ms)
 * -l, --loop         loop mode: send pings forever
 * -m, --all          use all IPs of provided hostnames (e.g. IPv4 and IPv6), use with -A
 * -M, --dontfrag     set the Don't Fragment flag
 * -O, --tos=N        set the type of service (tos) flag on the ICMP packets
 * -p, --period=MSEC  interval between ping packets to one target (in ms)
 * (in loop and count modes, default: 1000 ms)
 * -r, --retry=N      number of retries (default: 3)
 * -R, --random       random packet data (to foil link data compression)
 * -S, --src=IP       set source address
 * -t, --timeout=MSEC individual target initial timeout (default: 500 ms,
 * except with -l/-c/-C, where it's the -p period up to 2000 ms)
 *
 * Output options:
 * -a, --alive        show targets that are alive
 * -A, --addr         show targets by address
 * -C, --vcount=N     same as -c, report results (not stats) in verbose format
 * -d, --rdns         show targets by name (force reverse-DNS lookup)
 * -D, --timestamp    print timestamp before each output line
 * -e, --elapsed      show elapsed time on return packets
 * -n, --name         show targets by name (reverse-DNS lookup for target IPs)
 * -N, --netdata      output compatible for netdata (-l -Q are required)
 * -o, --outage       show the accumulated outage time (lost packets * packet interval)
 * -q, --quiet        quiet (don't show per-target/per-ping results)
 * -Q, --squiet=SECS  same as -q, but add interval summary every SECS seconds
 * -s, --stats        print final stats
 * -u, --unreach      show targets that are unreachable
 * -v, --version      show version
 * -x, --reachable=N  shows if >=N hosts are reachable or not
 * -X, --fast-reachable=N exits true immediately when N hosts are found
 */
final class Fping implements FpingInterface
{
    public string $binaryPath;

    public string $inputFilePath;

    public string $outputFilePath;

    public int $size = 56;

    public float $backoff = 1.5;

    public int $count = 1;

    public int $timeToLive = 64;

    public float $interval = 0.1;

    public bool $resolveAllHostnameIpAddresses = false;

    public bool $dontFragment = false;

    public string $typeOfService = '0x00';

    public float $intervalPerHost = 1000;

    public int $retries = 1;

    public bool $sendRandomData = false;

    public string $sourceAddress;

    public int $timeout = 500;

    public bool $showByIp = true;

    public bool $quiet = true;

    public function __construct(
        private readonly PendingProcess $process,
        private readonly BinFinder $binFinder,
        private readonly Repository $config,
        private readonly Filesystem $filesystem,
    ) {
        $this->generateOutputFilePath();
    }

    public function binary(string $binaryPath): self
    {
        Assert::fileExists($binaryPath, 'The fping binary could not be found at the given path');

        is_executable($binaryPath) || throw new RuntimeException("The fping binary at {$binaryPath} is not executable");

        $canonicalized = canonicalize($binaryPath);

        Assert::string($canonicalized);

        $this->binaryPath = $canonicalized;

        return $this;
    }

    public function inputFilePath(string $inputFile): self
    {
        Assert::fileExists($inputFile, 'The input file could not be found at the given path');

        $canonicalized = canonicalize($inputFile);

        Assert::string($canonicalized);

        $this->inputFilePath = $canonicalized;

        return $this;
    }

    public function outputFilePath(string $outputFile): self
    {
        Assert::fileExists($outputFile, 'The output file could not be found at the given path');

        $canonicalized = canonicalize($outputFile);

        Assert::string($canonicalized);

        $this->outputFilePath = $canonicalized;

        return $this;
    }

    private function generateOutputFilePath(): void
    {
        $this->outputFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('fping_output_'.Str::random(10).'.txt');
    }

    public function size(int $bytes): self
    {
        Assert::positiveInteger($bytes, 'The size must be a positive integer');

        $this->size = $bytes;

        return $this;
    }

    public function backoffFactor(float $backoff): self
    {
        Assert::greaterThan($backoff, 1, 'The backoff factor must be greater than 1');

        $this->backoff = $backoff;

        return $this;
    }

    public function count(int $count): self
    {
        Assert::positiveInteger($count, 'The count must be a positive integer');

        $this->count = $count;

        return $this;
    }

    public function timeToLive(int $ttl): self
    {
        Assert::positiveInteger($ttl, 'The time to live must be a positive integer');

        $this->timeToLive = $ttl;

        return $this;
    }

    public function interval(float $interval): self
    {
        Assert::greaterThanEq($interval, 0, 'The interval must be greater than or equal to 0');

        $this->interval = $interval;

        return $this;
    }

    public function resolveAllHostnameIpAddresses(bool $bool = true): self
    {
        $this->resolveAllHostnameIpAddresses = $bool;

        return $this;
    }

    public function dontFragment(bool $bool = true): self
    {
        $this->dontFragment = $bool;

        return $this;
    }

    public function typeOfService(string $tos): self
    {
        $this->typeOfService = $tos;

        return $this;
    }

    public function intervalPerHost(float $interval): self
    {
        Assert::greaterThanEq($interval, 0, 'The interval per host must be greater than or equal to 0');

        $this->intervalPerHost = $interval;

        return $this;
    }

    public function retries(int $retries): self
    {
        Assert::positiveInteger($retries, 'The retries must be a positive integer');

        $this->retries = $retries;

        return $this;
    }

    public function sendRandomData(bool $bool = true): self
    {
        $this->sendRandomData = $bool;

        return $this;
    }

    public function sourceAddress(string $sourceAddress): self
    {
        $this->sourceAddress = $sourceAddress;

        return $this;
    }

    public function timeout(int $timeout): self
    {
        Assert::positiveInteger($timeout, 'The timeout must be a positive integer');

        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return array<int, PingResultDTO>
     */
    public function execute(): array
    {
        $fpingPrefix = $this->config->get('fping.binaries.prefix');
        $fpingBinaryDirectory = $this->config->get('fping.binaries.directory');

        Assert::string($fpingPrefix);
        Assert::string($fpingBinaryDirectory);

        $fpingBinary = $this->binFinder->prefix($fpingPrefix)
            ->inDirectory($fpingBinaryDirectory)
            ->find();

        $command = [
            $fpingBinary,
            '--file',
            $this->inputFilePath,
            '--size',
            $this->size,
            '--backoff',
            $this->backoff,
            '--vcount',
            $this->count,
            '--ttl',
            $this->timeToLive,
            '--interval',
            $this->interval,
            '--tos',
            $this->typeOfService,
            '--period',
            $this->intervalPerHost,
            '--retry',
            $this->retries,
            '--timeout',
            $this->timeout,
        ];

        if ($this->resolveAllHostnameIpAddresses === true) {
            $command[] = '--all';
        }

        if ($this->dontFragment === true) {
            $command[] = '--dontfrag';
        }

        if ($this->sendRandomData === true) {
            $command[] = '--random';
        }

        if (isset($this->sourceAddress) === true) {
            $command[] = '--src';
            $command[] = $this->sourceAddress;
        }

        if ($this->showByIp === true) {
            $command[] = '--addr';
        }

        if ($this->quiet === true) {
            $command[] = '--quiet';
        }

        Assert::integer($timeout = $this->config->get('fping.process_timeout'));

        $result = $this->process
            ->timeout($timeout)
            ->command(implode(' ', $command)." 2>&1 | tee {$this->outputFilePath}")
            ->run()
            ->throw();

        $return = $this->filesystem
            ->lines($this->outputFilePath)
            ->reject(fn (string $line) => mb_strlen(trim($line)) === 0)
            ->map($this->createFpingDto(...))
            ->toArray();

        Assert::allIsInstanceOf($return, PingResultDTO::class);

        return $return;
    }

    private function createFpingDto(string $line, int $index): PingResultDTO
    {
        $str = Str::of($line);

        $ip = $str->before(':')->trim()->toString();
        $sequences = explode(' ', $str->after(':')->trim()->toString());

        return new PingResultDTO(
            $ip,
            IpValidator::make($ip)->determineType(),
            array_map(fn (string $sequence, int $index) => $this->createSequence($sequence, $index), $sequences, array_keys($sequences))
        );
    }

    private function createSequence(string $sequence, int $index): Sequence
    {
        $healthy = is_numeric($trimmed = trim($sequence));

        return new Sequence(
            $index + 1,
            ! $healthy,
            $healthy ? (float) $trimmed : null
        );
    }
}
