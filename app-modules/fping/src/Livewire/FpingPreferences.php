<?php

declare(strict_types=1);

namespace XbNz\Fping\Livewire;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use XbNz\Fping\DTOs\FpingPreferencesDto;
use XbNz\Fping\Livewire\Forms\FpingPreferencesForm;
use XbNz\Fping\Models\FpingPreferences as FpingPreferencesModel;

final class FpingPreferences extends Component
{
    public FpingPreferencesForm $form;

    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    /**
     * @return Collection<int, FpingPreferencesDto>
     */
    #[Computed]
    public function fpingPreferencesRecords(): Collection
    {
        return FpingPreferencesDto::collect(FpingPreferencesModel::query()->get());
    }

    public function createNewPreferencesRecord(): void
    {
        $this->form->createWithSensibleDefaults();
    }

    public function selectTab(int $tab): void
    {
        $this->resetErrorBag();
        $this->form->setFpingPreferences(FpingPreferencesModel::query()->findOrFail($tab)->getData());
        $this->dispatch('refreshComponent');
    }

    public function enable(): void
    {
        $this->form->enable();
    }

    public function delete(): void
    {
        $this->form->delete();
    }

    public function updated(): void
    {
        $this->form->validate();

        $this->form->update();
    }

    public function mount(): void
    {
        $this->form->setFpingPreferences($this->fpingPreferencesRecords()->firstOrFail());
    }

    public function render(): View
    {
        return view('fping::livewire.fping-preferences');
    }
}
