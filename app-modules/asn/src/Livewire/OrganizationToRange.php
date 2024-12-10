<?php

declare(strict_types=1);

namespace XbNz\Asn\Livewire;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use stdClass;
use XbNz\Asn\Contracts\AsnToRangeInterface;
use XbNz\Asn\Enums\Provider;
use XbNz\Asn\ValueObject\Asn;
use XbNz\Asn\ValueObject\IpRange;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Contracts\RapidParserInterface;
use XbNz\RouteviewsIntegration\RouteViewsAsnToRange;

#[Layout('components.layouts.secondary-window')]
final class OrganizationToRange extends Component
{
    public string $searchTerm = '';

    /**
     * @var array<int, string>
     */
    public array $selectedAsNumbers = [];

    public Provider $selectedProvider;

    public string $ranges = '';

    #[Locked]
    public string $inputFile;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'searchTerm' => ['string'],
            'selectedAsNumbers' => ['required', 'array', 'min:1'],
            'selectedAsNumbers.*' => ['min:1', 'numeric'],
            'selectedProvider' => ['required', Rule::enum(Provider::class)],
        ];
    }

    /**
     * @return array<int, Asn>
     */
    #[Computed]
    public function organizations(): array
    {
        return app(RouteViewsAsnToRange::class)
            ->organization($this->searchTerm)
            ->all()
            ->limit(10)
            ->get()
            ->map(fn (stdClass $organization) => new Asn($organization->organization, $organization->asn))
            ->toArray();
    }

    public function convert(
        Filesystem $filesystem
    ): void {
        $this->validate();

        $this->ranges = '';

        $this->inputFile = TemporaryDirectory::make()
            ->force()
            ->create()
            ->path('input_'.Str::random(5).'.txt');

        touch($this->inputFile);

        $providers = app()->tagged('asn-to-range');

        $asnToRange = Collection::make(iterator_to_array($providers))
            ->filter(fn (AsnToRangeInterface $asnToRange) => $asnToRange->supports($this->selectedProvider))
            ->sole();

        foreach ($this->selectedAsNumbers as $asNumber) {
            $ipRanges = $asnToRange->asNumber((int) $asNumber)->execute();

            $filesystem->append($this->inputFile, $ipRanges->map(fn (IpRange $ipRange) => "{$ipRange->startIp}-{$ipRange->endIp}")->implode(PHP_EOL).PHP_EOL);

            foreach ($ipRanges as $ipRange) {
                $this->ranges .= $ipRange->startIp.' - '.$ipRange->endIp.PHP_EOL;
            }
        }
    }

    public function addToMyIpAddresses(
        ImportIpAddressesAction $importIpAddressesAction,
        RapidParserInterface $rapidParser
    ): void {
        $importIpAddressesAction->handle($rapidParser->inputFilePath($this->inputFile)->parse());
    }

    public function mount(): void
    {
        $this->selectedProvider = Provider::RouteViews;
    }

    public function render(): View
    {
        return view('asn::livewire.organization-to-range', [
            'providers' => array_column(array_filter(Provider::cases(), fn (Provider $provider) => $provider->canBeUsedInProduction()), 'value'),
        ]);
    }
}
