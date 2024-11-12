<?php

declare(strict_types=1);

namespace XbNz\Ping\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;
use Native\Laravel\Facades\Window;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Fping\Contracts\FpingInterface;

final class Ping extends Component
{
    public string $target = '1.1.1.1';

    public int $count = 5;

    public int $timeBetweenRequests = 500;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target' => ['required', 'string'],
            'count' => ['integer', 'min:1', 'max:100'],
            'timeBetweenRequests' => ['integer', 'min:100', 'max:50000'],
        ];
    }

    public function ping(): void
    {
        $this->validate();

        $temporaryFilePath = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('ping.txt');

        file_put_contents($temporaryFilePath, $this->target);

        $pingResultDto = App::make(FpingInterface::class)
            ->inputFilePath($temporaryFilePath)
            ->count($this->count)
            ->intervalPerHost($this->timeBetweenRequests)
            ->execute()[0];

        Session::put('ping-result', $pingResultDto);

        Window::open('ping-results')
            ->route('ping-results')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->height(490)
            ->width(775)
            ->minHeight(490)
            ->minWidth(775);
    }

    public function render(): View
    {
        return view('ping::livewire.ping');
    }
}
