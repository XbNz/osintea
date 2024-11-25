<?php

declare(strict_types=1);

namespace XbNz\Shared\Exceptions;

use Exception;

final class BinaryNotExecutableException extends Exception
{
    public static function for(
        string $prefix,
        string $directory,
        string $os,
        string $arch
    ): self {
        return new self(
            sprintf(
                'File found with prefix `%s` in directory `%s` for OS `%s` and architecture `%s`, but it is not executable',
                $prefix,
                $directory,
                $os,
                $arch
            )
        );
    }
}
