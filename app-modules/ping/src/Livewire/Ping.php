<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Flux\Flux;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Fping\Fping;

final class Ping extends Component
{
    public string $target = '1.1.1.1';

    public int $count = 1;

    public int $timeBetweenRequests = 100;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target' => [
                'required',
            ],
            'count' => ['integer', 'min:1', 'max:100'],
            'timeBetweenRequests' => ['integer', 'min:100', 'max:50000'],
        ];
    }

    public function ping(): void
    {
        $this->validate();

        Flux::toast("Pinging {$this->target}");

        $temporaryFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('ping.txt');

        file_put_contents($temporaryFilePath, $this->target);

        $pingResultDto = App::make(Fping::class)
            ->inputFilePath($temporaryFilePath)
            ->count($this->count)
            ->intervalPerHost($this->timeBetweenRequests)
            ->execute()[0];

        Session::put('ping-result', $pingResultDto);

        $this->redirectRoute('ping-results', navigate: true);
    }

    public function render(): View
    {
        return view('ping::livewire.ping');
    }
}
