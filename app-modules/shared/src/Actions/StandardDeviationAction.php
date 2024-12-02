<?php

declare(strict_types=1);

namespace XbNz\Shared\Actions;

final class StandardDeviationAction
{
    /**
     * @param  array<int, float>  $values
     */
    public function handle(array $values): float
    {
        $mean = array_sum($values) / count($values);

        $variance = array_sum(
            array_map(
                fn (float $value) => ($value - $mean) ** 2,
                $values
            )
        ) / count($values);

        return sqrt($variance);
    }
}
