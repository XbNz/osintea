<?php

declare(strict_types=1);

namespace XbNz\Ip\Contracts;

interface RapidParserInterface
{
    public function inputFilePath(string $inputFile): self;

    public function outputFilePath(string $outputFile): self;

    public function timeout(int $seconds): self;

    public function sampleSize(int $totalCount): self;

    public function parse(): string;
}
