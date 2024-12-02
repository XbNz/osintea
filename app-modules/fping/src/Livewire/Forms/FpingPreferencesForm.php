<?php

declare(strict_types=1);

namespace XbNz\Fping\Livewire\Forms;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use XbNz\Fping\DTOs\CreateFpingPreferencesDto;
use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\DTOs\UpdateFpingPreferencesDto;
use XbNz\Fping\Events\Intentions\CreateFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\DeleteFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\EnableFpingPreferencesIntention;
use XbNz\Fping\Events\Intentions\UpdateFpingPreferencesIntention;
use XbNz\Fping\Models\FpingPreferences;

final class FpingPreferencesForm extends Form
{
    #[Locked]
    public int $id;

    public string $name;

    public int $size;

    public float $backoff;

    public int $count;

    public int $ttl;

    public int $interval;

    public int $interval_per_target;

    public string $type_of_service;

    public int $retries;

    public int $timeout;

    public bool $dont_fragment;

    public bool $send_random_data;

    public bool $enabled;

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(FpingPreferences::class, 'id')],
            'name' => ['required', 'string'],
            'size' => ['required', 'integer', 'min:0'],
            'backoff' => ['required', 'numeric', 'min:0'],
            'count' => ['required', 'integer', 'min:1'],
            'ttl' => ['required', 'integer', 'min:0'],
            'interval' => ['required', 'integer', 'min:1'],
            'interval_per_target' => ['required', 'integer', 'min:1'],
            'type_of_service' => ['required', 'string'],
            'retries' => ['required', 'integer', 'min:0'],
            'timeout' => ['required', 'integer', 'min:1'],
            'dont_fragment' => ['required', 'boolean'],
            'send_random_data' => ['required', 'boolean'],
            'enabled' => ['required', 'boolean'],
        ];
    }

    public function setFpingPreferences(FpingPreferencesDto $fpingPreferences): void
    {
        $this->id = $fpingPreferences->id;
        $this->name = $fpingPreferences->name;
        $this->size = $fpingPreferences->size;
        $this->backoff = $fpingPreferences->backoff;
        $this->count = $fpingPreferences->count;
        $this->ttl = $fpingPreferences->ttl;
        $this->interval = $fpingPreferences->interval;
        $this->interval_per_target = $fpingPreferences->interval_per_target;
        $this->type_of_service = $fpingPreferences->type_of_service;
        $this->retries = $fpingPreferences->retries;
        $this->timeout = $fpingPreferences->timeout;
        $this->dont_fragment = $fpingPreferences->dont_fragment;
        $this->send_random_data = $fpingPreferences->send_random_data;
        $this->enabled = $fpingPreferences->enabled;
    }

    public function createWithSensibleDefaults(): void
    {
        app(Dispatcher::class)->dispatch(new CreateFpingPreferencesIntention(
            new CreateFpingPreferencesDto(
                Str::random(6),
                56,
                1.5,
                1,
                64,
                10,
                1000,
                '0x00',
                1,
                500,
                false,
                false,
            )
        ));
    }

    public function delete(): void
    {
        $record = FpingPreferences::query()->findOrFail($this->id)->getData();

        if ($record->enabled === true) {
            return;
        }

        app(Dispatcher::class)->dispatch(new DeleteFpingPreferencesIntention($record));
    }

    public function enable(): void
    {
        $record = FpingPreferences::query()->findOrFail($this->id)->getData();

        if ($record->enabled === true) {
            return;
        }

        app(Dispatcher::class)->dispatch(new EnableFpingPreferencesIntention($record));
    }

    public function update(): void
    {
        $this->validate();

        app(Dispatcher::class)->dispatch(new UpdateFpingPreferencesIntention(
            new UpdateFpingPreferencesDto(
                $this->id,
                $this->name,
                $this->size,
                $this->backoff,
                $this->count,
                $this->ttl,
                $this->interval,
                $this->interval_per_target,
                $this->type_of_service,
                $this->retries,
                $this->timeout,
                $this->dont_fragment,
                $this->send_random_data,
            )
        ));
    }
}
