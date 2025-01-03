<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class FilterRoundTripTime
{
    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->roundTripTimeFilter->canBeApplied() === false) {
            return $transporter;
        }

        $transporter->query
            ->where(fn (Builder $query) => $query->whereHas('pingSequences', function (Builder $query) use ($transporter): void {
                $query
                    ->groupBy('ping_sequences.ip_address_id')
                    ->when($transporter->roundTripTimeFilter->minFloor, function (Builder $query, $minFloor): void {
                        $query->having(DB::raw('MIN(ping_sequences.round_trip_time)'), '>=', $minFloor);
                    })
                    ->when($transporter->roundTripTimeFilter->maxFloor, function (Builder $query, $maxFloor): void {
                        $query->having(DB::raw('MIN(ping_sequences.round_trip_time)'), '<=', $maxFloor);
                    })
                    ->when($transporter->roundTripTimeFilter->minAverage, function (Builder $query, $minAverage): void {
                        $query->having(DB::raw('AVG(ping_sequences.round_trip_time)'), '>=', $minAverage);
                    })
                    ->when($transporter->roundTripTimeFilter->maxAverage, function (Builder $query, $maxAverage): void {
                        $query->having(DB::raw('AVG(ping_sequences.round_trip_time)'), '<=', $maxAverage);
                    })
                    ->when($transporter->roundTripTimeFilter->minCeiling, function (Builder $query, $minCeiling): void {
                        $query->having(DB::raw('MAX(ping_sequences.round_trip_time)'), '>=', $minCeiling);
                    })
                    ->when($transporter->roundTripTimeFilter->maxCeiling, function (Builder $query, $maxCeiling): void {
                        $query->having(DB::raw('MAX(ping_sequences.round_trip_time)'), '<=', $maxCeiling);
                    });
            }));

        return $transporter;
    }
}
