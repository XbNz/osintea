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

final class TestRouteViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-routeviews';

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
        $csv = Http::get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/asn/asn-ipv4.csv')
            ->throw()
            ->getBody();

        $temporaryDir = TemporaryDirectory::make()
            ->force()
            ->create();

        $temporaryCsv = $temporaryDir->path('geolite2-city-ipv4.csv');

        touch($temporaryCsv);

        while ($csv->eof() === false) {
            file_put_contents($temporaryCsv, $csv->read(4096), FILE_APPEND);
        }

        DB::connection('routeviews-asn')
            ->table('ipv4')
            ->delete();

        File::lines($temporaryCsv)
            ->chunk(2000)
            ->each(function (LazyCollection $chunk): void {
                $data = $chunk->map(fn (string $line) => Str::of($line)->explode(','))
                    ->map(function (Collection $line) {
                        return [
                            'start_ip' => $line->get(0),
                            'end_ip' => $line->get(1),
                            'asn' => $line->get(2),
                            'organization' => $line->get(3),
                        ];
                    });

                DB::connection('routeviews-asn')
                    ->table('ipv4')
                    ->insertOrIgnore($data->toArray());
            });
    }
}
