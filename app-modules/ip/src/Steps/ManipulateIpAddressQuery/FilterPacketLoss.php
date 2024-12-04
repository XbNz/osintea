<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class FilterPacketLoss
{
    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->packetLossFilter->canBeApplied() === false) {
            return $transporter;
        }

        $transporter->query
            ->whereHas('pingSequences', function (Builder $query) use ($transporter): void {
                $query
                    ->groupBy('ping_sequences.ip_address_id')
                    ->when($transporter->packetLossFilter->minPercent, function (Builder $query, $min): void {
                        $query->having(DB::raw('CAST(SUM(ping_sequences.loss) AS REAL) / COUNT(ping_sequences.id) * 100'), '>=', $min);
                    })
                    ->when($transporter->packetLossFilter->maxPercent, function (Builder $query, $max): void {
                        $query->having(DB::raw('CAST(SUM(ping_sequences.loss) AS REAL) / COUNT(ping_sequences.id) * 100'), '<=', $max);
                    });
            });

        return $transporter;
    }
}
