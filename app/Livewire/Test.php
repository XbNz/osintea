<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Process;
use Livewire\Component;

class Test extends Component
{
    public string $ipAddress = '';
    public string $response = '';

    public function fping(): void
    {
        dump(base_path('bin/fping'));
        $this->response = Process::run([base_path('bin/fping'), '-c', '1', $this->ipAddress])->throw()->output();
    }

    public function render()
    {
        return view('livewire.test');
    }
}
