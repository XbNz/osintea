<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

final class SortByAverageRtt
{
    public function handle(Transporter $transporter): Transporter
    {
        $transporter->query
            ->withAvg('pingSequences', 'round_trip_time')
            ->orderBy('ping_sequences_avg_round_trip_time', $transporter->direction);

        return $transporter;
    }
}
