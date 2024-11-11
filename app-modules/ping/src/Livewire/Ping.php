<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Illuminate\View\View;
use Livewire\Component;

final class Ping extends Component
{
    public string $target = '1.1.1.1';
    public int $count = 1;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target' => [
                'required',
            ],
            'count' => ['integer', 'min:1', 'max:10'],
        ];
    }

    public function ping(): void
    {
        $this->validate();
    }

    public function render(): View
    {
        return view('ping::livewire.ping');
    }
}
