<?php

declare(strict_types=1);

namespace XbNz\Asn\Actions;

use XbNz\Asn\DTOs\AsnDto;
use XbNz\Asn\DTOs\CreateAsnDto;
use XbNz\Asn\Model\Asn;

final class CreateAsnAction
{
    public function handle(CreateAsnDto $createAsnDto): AsnDto
    {
        return Asn::query()->create([
            'ip_address_id' => $createAsnDto->ip->id,
            'organization' => $createAsnDto->asn->organization,
            'as_number' => $createAsnDto->asn->asNumber,
        ])->refresh()->getData();
    }
}
