<?php

declare(strict_types=1);

namespace XbNz\Shared\Exceptions;

use Exception;

final class BinaryNotFoundException extends Exception
{
    public static function for(
        string $prefix,
        string $directory,
        string $os,
        string $arch
    ): self {
        return new self(
            sprintf(
                'No binary found with prefix `%s` in directory `%s` for OS `%s` and architecture `%s`',
                $prefix,
                $directory,
                $os,
                $arch
            )
        );
    }
}
