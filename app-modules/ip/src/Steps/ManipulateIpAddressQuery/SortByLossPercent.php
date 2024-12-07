<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

final class SortByLossPercent
{
    public function handle(Transporter $transporter): Transporter
    {
        $transporter->query
            ->withAvg('pingSequences', 'loss')
            ->orderBy('ping_sequences_avg_loss', $transporter->direction);

        return $transporter;
    }
}
