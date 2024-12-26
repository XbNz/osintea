<?php

declare(strict_types=1);

namespace XbNz\Location\Livewire;

use GeoJson\GeoJson;
use GeoJson\Geometry\Polygon;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Webmozart\Assert\Assert;
use XbNz\Asn\Contracts\AsnToRangeInterface;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Contracts\RapidParserInterface;
use XbNz\Location\Contracts\PolygonToRangeInterface;
use XbNz\Location\Enums\Provider;
use XbNz\Location\ValueObjects\IpRange;

#[Layout('components.layouts.secondary-window')]
final class LocationToRange extends Component
{
    public int $ipTypeMask = PolygonToRangeInterface::FILTER_IPV4;

    #[Locked]
    public string $ranges = '';

    #[Locked]
    public string $inputFile;

    public Provider $selectedProvider;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'selectedProvider' => ['required', Rule::enum(Provider::class)],
        ];
    }

    /**
     * @param  array<mixed>  $geoJson
     */
    public function addPolygon(array $geoJson): void
    {
        $this->validate();

        if (isset($this->inputFile) === false) {
            $this->inputFile = TemporaryDirectory::make()
                ->force()
                ->create()
                ->path('input_'.Str::random(5).'.txt');
        }

        $fileSystem = app(Filesystem::class);

        $geoJsonObject = GeoJson::jsonUnserialize($geoJson);

        Assert::isInstanceOf($polygon = $geoJsonObject->getFeatures()[0]->getGeometry(), Polygon::class);

        $providers = app()->tagged('polygon-to-range');

        $polygonToRange = Collection::make(iterator_to_array($providers))
            ->filter(fn (PolygonToRangeInterface $polygonToRange) => $polygonToRange->supports($this->selectedProvider))
            ->sole();

        $ranges = $polygonToRange->filterIpType($this->ipTypeMask)
            ->addPolygon($polygon)
            ->execute();

        $this->ranges .= $ranges
            ->each(fn (IpRange $range) => $fileSystem->append($this->inputFile, "{$range->startIp}-{$range->endIp}".PHP_EOL))
            ->map(fn (IpRange $range) => "{$range->startIp} - {$range->endIp}")->implode(PHP_EOL).PHP_EOL;
    }

    public function addToMyIpAddresses(
        ImportIpAddressesAction $importIpAddressesAction,
        RapidParserInterface $rapidParser
    ): void {
        $importIpAddressesAction->handle($rapidParser->inputFilePath($this->inputFile)->parse());
    }

    public function limitV4(): void
    {
        $this->ipTypeMask = AsnToRangeInterface::FILTER_IPV4;
    }

    public function limitV6(): void
    {
        $this->ipTypeMask = AsnToRangeInterface::FILTER_IPV6;
    }

    public function clearIpTypeLimits(): void
    {
        $this->ipTypeMask = AsnToRangeInterface::FILTER_IPV4 | AsnToRangeInterface::FILTER_IPV6;
    }

    public function render(): View
    {
        return view('location::livewire.location-to-range', [
            'providers' => array_column(array_filter(Provider::cases(), fn (Provider $provider) => $provider->canBeUsedInProduction()), 'value'),
        ]);
    }
}
