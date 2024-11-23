<?php

declare(strict_types=1);

namespace XbNz\Ping\Steps\LoopingStandardInPingWorker;

use Illuminate\Support\Str;
use XbNz\Ping\DTOs\RemoveTargetRequestDto;

final class GatherTargetToRemove
{
    public function handle(Transporter $transporter): Transporter
    {
        if (Str::of($transporter->data)->startsWith(['target-remove:']) === false) {
            return $transporter;
        }

        $target = Str::after($transporter->data, 'target-remove:');

        if (filter_var($target, FILTER_VALIDATE_IP) === false) {
            return $transporter;
        }

        $transporter->removeTargetRequestDto = new RemoveTargetRequestDto(
            $target
        );

        return $transporter;
    }
}
