<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Process;
use Livewire\Component;

final class Test extends Component
{
    public string $ipAddress = '';

    public string $response = '';

    public function fping(): void
    {
        dump(base_path('bin/fping_darwin_arm64'));
        $this->response = Process::run([base_path('bin/fping_darwin_arm64'), '-c', '1', $this->ipAddress])->throw()->output();
    }

    public function render()
    {
        return view('livewire.test');
    }
}
