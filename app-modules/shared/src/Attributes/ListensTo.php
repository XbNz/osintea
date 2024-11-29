<?php

declare(strict_types=1);

namespace XbNz\Shared\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class ListensTo
{
    /**
     * @param  class-string  $event
     */
    public function __construct(
        public string $event,
    ) {}
}
