<?php

declare(strict_types=1);

namespace XbNz\Ping\Steps\LoopingStandardInPingWorker;

use Illuminate\Support\Str;
use XbNz\Ping\DTOs\AddTargetRequestDto;

final class GatherTargetToAdd
{
    public function handle(Transporter $transporter): Transporter
    {
        if (Str::of($transporter->data)->startsWith(['target-add:']) === false) {
            return $transporter;
        }

        $target = Str::between($transporter->data, 'target-add:', '::');

        $interval = Str::afterLast($transporter->data, ':');

        if (filter_var($target, FILTER_VALIDATE_IP) === false) {
            return $transporter;
        }

        if (is_numeric($interval) === false) {
            return $transporter;
        }

        $transporter->addTargetRequestDto = new AddTargetRequestDto(
            $target,
            (int) $interval
        );

        return $transporter;
    }
}
