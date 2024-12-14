<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Webmozart\Assert\Assert;

final class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $gzip = Http::get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/geolite2-city/geolite2-city-ipv4.csv.gz')
            ->throw()
            ->getBody();

        $temporaryDir = TemporaryDirectory::make()
            ->force()
            ->create();

        $temporaryGzip = $temporaryDir->path('geolite2-city-ipv4.csv.gz');
        $temporaryCsv = $temporaryDir->path('geolite2-city-ipv4.csv');

        touch($temporaryGzip);
        touch($temporaryCsv);

        while ($gzip->eof() === false) {
            file_put_contents($temporaryGzip, $gzip->read(4096), FILE_APPEND);
        }

        $gzFile = gzopen($temporaryGzip, 'rb');
        $outFile = fopen($temporaryCsv, 'wb');

        Assert::resource($gzFile);
        Assert::resource($outFile);

        while (gzeof($gzFile) === false) {
            $chunk = gzread($gzFile, 4096);
            fwrite($outFile, $chunk);
        }

        // Clean up
        fclose($outFile);
        gzclose($gzFile);
        unlink($temporaryGzip);

        DB::connection('routeviews-asn')
            ->table('ipv4')
            ->delete();

        File::lines($temporaryCsv)
            ->chunk(100)
            ->each(function (LazyCollection $chunk): void {
                $data = $chunk->map(fn (string $line) => Str::of($line)->explode(','))
                    ->map(function (Collection $line) {
                        return [
                            'start_ip' => $line->get(0),
                            'end_ip' => $line->get(1),
                            'asn' => $line->get(2),
                        ];
                    });

                DB::connection('routeviews-asn')
                    ->table('ipv4')
                    ->insert($data->toArray());
            });
    }
}
