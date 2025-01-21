<?php

declare(strict_types=1);

namespace XbNz\Preferences\Livewire;

use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use XbNz\Preferences\DTOs\MasscanPreferencesDto;
use XbNz\Preferences\Livewire\Forms\MasscanPreferencesForm;
use XbNz\Preferences\Models\MasscanPreferences as MasscanPreferencesModel;

final class MasscanPreferences extends Component
{
    public MasscanPreferencesForm $form;

    /**
     * @return Collection<int, MasscanPreferencesDto>
     */
    #[Computed]
    public function masscanPreferencesRecords(): Collection
    {
        return MasscanPreferencesDto::collect(MasscanPreferencesModel::query()->get());
    }

    public function createNewPreferencesRecord(): void
    {
        $this->form->createWithSensibleDefaults();
    }

    public function selectTab(int $tab): void
    {
        $this->resetErrorBag();
        $this->form->setMasscanPreferences(MasscanPreferencesModel::query()->findOrFail($tab)->getData());
        $this->dispatch('refreshComponent');
    }

    public function enable(): void
    {
        $this->form->enable();
    }

    public function delete(): void
    {
        $this->form->delete();

        Flux::toast('Preferences removed.', 'Deleted', variant: 'success');
    }

    public function updated(): void
    {
        $this->form->update();

        Flux::toast('Changes saved.', 'Saved', 2000, 'success');
    }

    public function mount(): void
    {
        $this->form->setMasscanPreferences($this->masscanPreferencesRecords()->firstOrFail());
    }

    public function render(): View
    {
        return view('preferences::livewire.masscan-preferences');
    }
}
