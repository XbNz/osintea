<?php

declare(strict_types=1);

namespace XbNz\Location\Console\Commands;

use Illuminate\Console\Command;

final class TestLocationCommand extends Command
{
    protected $signature = 'test:location';

    protected $description = 'Command description';

    public function handle(): void {}
}
