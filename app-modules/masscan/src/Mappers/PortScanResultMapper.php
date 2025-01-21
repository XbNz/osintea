<?php

declare(strict_types=1);

namespace XbNz\Masscan\Mappers;

use Illuminate\Support\Arr;
use Psl\Type;
use XbNz\Port\DTOs\PortScanResultDto;
use XbNz\Shared\Enums\PortState;
use XbNz\Shared\Enums\ProtocolType;
use XbNz\Shared\IpValidator;

final class PortScanResultMapper
{
    /**
     * @param  array<mixed>  $masscanJson
     */
    public static function map(array $masscanJson, ProtocolType $protocolType): PortScanResultDto
    {
        $sanitized = Type\shape([
            'ip' => Type\non_empty_string(),
            'timestamp' => Type\non_empty_string(),
            'ports' => Type\non_empty_vec(
                Type\shape([
                    'port' => Type\int(),
                    'status' => Type\backed_enum(PortState::class),
                ]),
            ),
        ])->coerce($masscanJson);

        return new PortScanResultDto(
            $sanitized['ip'],
            IpValidator::make($sanitized['ip'])->determineType(),
            $protocolType,
            Arr::mapWithKeys($sanitized['ports'], fn (array $port) => [$port['port'] => $port['status']]),
        );
    }
}
