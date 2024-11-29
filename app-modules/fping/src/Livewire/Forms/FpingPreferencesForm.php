<?php

declare(strict_types=1);

namespace XbNz\Fping\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Form;
use XbNz\Fping\DTOs\FpingPreferencesDto;

final class FpingPreferencesForm extends Form
{
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

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique('fping_preferences', 'name')],
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

    public function update(): void
    {
        $this->validate();

    }
}
