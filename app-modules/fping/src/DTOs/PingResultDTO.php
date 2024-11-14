<?php

declare(strict_types=1);

namespace XbNz\Fping\DTOs;

use XbNz\Fping\ValueObjects\Sequence;
use XbNz\Shared\ValueObjects\IpType;

final class PingResultDTO
{
    /**
     * @param  array<int, Sequence>  $sequences
     */
    public function __construct(
        public readonly string $ip,
        public readonly IpType $ipType,
        public readonly array $sequences
    ) {}

    public function toArray(): array
    {
        return [
            'ip' => $this->ip,
            'ipType' => $this->ipType->value,
            'sequences' => array_map(
                fn (Sequence $sequence) => $sequence->toArray(),
                $this->sequences
            ),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            ip: $data['ip'],
            ipType: IpType::from($data['ipType']),
            sequences: array_map(
                fn (array $sequence) => Sequence::fromArray($sequence),
                $data['sequences']
            ),
        );
    }
}
