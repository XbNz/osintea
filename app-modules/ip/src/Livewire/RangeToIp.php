<?php

declare(strict_types=1);

namespace XbNz\Ip\Livewire;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Ip\Actions\CreateIpAddressAction;
use XbNz\Ip\Contracts\RapidParserInterface;
use XbNz\Shared\IpValidator;

#[Layout('components.layouts.secondary-window')]
final class RangeToIp extends Component
{
    public string $rangeList = '';

    #[Locked]
    public string $ipList = '';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rangeList' => ['required'],
        ];
    }

    public function convert(
        RapidParserInterface $rapidParser,
        Filesystem $filesystem
    ): void {
        $this->validate();

        $rangeList = explode(PHP_EOL, $this->rangeList);

        $inputFile = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input_'.Str::random(5).'.txt');

        touch($inputFile);

        foreach ($rangeList as $range) {
            $filesystem->append($inputFile, $range.PHP_EOL);
        }

        $outputFilePath = $rapidParser->inputFilePath($inputFile)->parse();

        $this->ipList = $filesystem->get($outputFilePath);
    }

    public function addToMyIpAddresses(CreateIpAddressAction $createIpAddressAction): void
    {
        $arrayIpList = array_filter(explode(PHP_EOL, $this->ipList), fn (string $ip) => empty($ip) === false);

        foreach ($arrayIpList as $ip) {
            IpValidator::make($ip)->assertValid();

            $createIpAddressAction->handle($ip);
        }
    }

    public function render()
    {
        return view('ip::livewire.range-to-ip');
    }
}
