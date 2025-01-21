<?php

declare(strict_types=1);

namespace XbNz\Preferences\Livewire\Forms;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use XbNz\Preferences\DTOs\CreateMasscanPreferencesDto;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\DTOs\UpdateMasscanPreferencesDto;
use XbNz\Preferences\Events\Intentions\CreateMasscanPreferencesIntention;
use XbNz\Preferences\Events\Intentions\DeleteMasscanPreferencesIntention;
use XbNz\Preferences\Events\Intentions\EnableMasscanPreferencesIntention;
use XbNz\Preferences\Events\Intentions\UpdateMasscanPreferencesIntention;
use XbNz\Preferences\Models\MasscanPreferences;

final class MasscanPreferencesForm extends Form
{
    #[Locked]
    public int $id;

    public string $name;

    public int $ttl;

    public int $rate;

    public ?string $adapter;

    public int $retries;

    public bool $enabled;

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', Rule::exists(MasscanPreferences::class, 'id')],
            'ttl' => ['required', 'integer'],
            'rate' => ['required', 'integer'],
            'adapter' => ['sometimes', 'string'],
            'retries' => ['required', 'integer'],
            'enabled' => ['required', 'boolean'],
            'name' => ['required', 'string'],
        ];
    }

    public function setMasscanPreferences(MasscanPreferencesDto $masscanPreferences): void
    {
        $this->id = $masscanPreferences->id;
        $this->name = $masscanPreferences->name;
        $this->ttl = $masscanPreferences->ttl;
        $this->rate = $masscanPreferences->rate;
        $this->adapter = $masscanPreferences->adapter;
        $this->retries = $masscanPreferences->retries;
        $this->enabled = $masscanPreferences->enabled;
    }

    public function createWithSensibleDefaults(): void
    {
        app(Dispatcher::class)->dispatch(new CreateMasscanPreferencesIntention(
            new CreateMasscanPreferencesDto(
                Str::random(6),
                55,
                10000,
                null,
                0,
            )
        ));
    }

    public function delete(): void
    {
        $record = MasscanPreferences::query()->findOrFail($this->id)->getData();

        if ($record->enabled === true) {
            return;
        }

        app(Dispatcher::class)->dispatch(new DeleteMasscanPreferencesIntention($record));
    }

    public function enable(): void
    {
        $record = MasscanPreferences::query()->findOrFail($this->id)->getData();

        if ($record->enabled === true) {
            return;
        }

        app(Dispatcher::class)->dispatch(new EnableMasscanPreferencesIntention($record));
    }

    public function update(): void
    {
        $this->validate();

        app(Dispatcher::class)->dispatch(new UpdateMasscanPreferencesIntention(
            new UpdateMasscanPreferencesDto(
                $this->id,
                $this->name,
                $this->ttl,
                $this->rate,
                $this->adapter ?? null,
                $this->retries,
                $this->enabled,
            )
        ));
    }
}
