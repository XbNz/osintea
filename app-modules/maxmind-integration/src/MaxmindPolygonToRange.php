<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration;

use GeoJson\Geometry\Polygon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use stdClass;
use XbNz\Location\Contracts\PolygonToRangeInterface;
use XbNz\Location\Enums\Provider;
use Psl\Type;
use XbNz\Location\ValueObjects\IpRange;
use XbNz\Shared\ValueObjects\Coordinates;
use XbNz\Shared\ValueObjects\IpType;

final class MaxmindPolygonToRange implements PolygonToRangeInterface
{
    private int $ipTypeMask = PolygonToRangeInterface::FILTER_IPV4;

    /**
     * @var Collection<int, Polygon>
     */
    public private(set) Collection $polygons;

    public function __construct(
        private readonly DatabaseManager $database,
    ) {
        $this->polygons = Collection::make();
    }

    public function filterIpType(int $filterMask): self
    {
        $this->ipTypeMask = $filterMask;

        return $this;
    }

    public function addPolygon(Polygon $polygon): self
    {
        $this->polygons->add($polygon);

        return $this;
    }

    /**
     * @return Collection<int, IpRange>
     */
    public function execute(): Collection
    {
        $multiPolygonStatement = $this->polygons
            ->map(function (Polygon $polygon) {
                $coordinates = Collection::make($polygon->getCoordinates())->flatten(1)
                    ->map(function (array $coordinate) {
                        $sanitized = Type\shape([
                            0 => Type\numeric_string(),
                            1 => Type\numeric_string(),
                        ])->coerce($coordinate);

                        return "{$sanitized[0]} {$sanitized[1]}";
                    })
                    ->join(',');

                return "(({$coordinates}))";
            })
            ->join(',');

        $v4Query = $this->database->table('maxmind_v4_geolocations')
            ->selectRaw('start_ip, end_ip, ST_AsText(coordinates) as coordinates')
            ->whereRaw("ST_CONTAINS(ST_GeomFromText('MULTIPOLYGON({$multiPolygonStatement})', 4326), coordinates)");

        $v6Query = $this->database->table('maxmind_v6_geolocations')
            ->selectRaw('start_ip, end_ip, ST_AsText(coordinates) as coordinates')
            ->whereRaw("ST_CONTAINS(ST_GeomFromText('MULTIPOLYGON({$multiPolygonStatement})', 4326), coordinates)");

        $requestedIpType = match ($this->ipTypeMask) {
            PolygonToRangeInterface::FILTER_IPV4 => [IpType::IPv4],
            PolygonToRangeInterface::FILTER_IPV6 => [IpType::IPv6],
            PolygonToRangeInterface::FILTER_IPV4 | PolygonToRangeInterface::FILTER_IPV6 => [IpType::IPv4, IpType::IPv6],
            default => throw new InvalidArgumentException('Need at least one IP type to filter by.'),
        };

        $v4Ranges = Collection::make();
        $v6Ranges = Collection::make();

        if (in_array(IpType::IPv4, $requestedIpType, true)) {
            $v4Ranges = $v4Query->get()
                ->map(function (stdClass $row) {
                    $sanitized = Type\shape([
                        'start_ip' => Type\non_empty_string(),
                        'end_ip' => Type\non_empty_string(),
                        'coordinates' => Type\non_empty_string(),
                    ])->coerce($row);

                    return new IpRange(
                        $sanitized['start_ip'],
                        $sanitized['end_ip'],
                        $this->createCoordinateObject($sanitized['coordinates']),
                        IpType::IPv4,
                    );
                });
        }

        if (in_array(IpType::IPv6, $requestedIpType, true)) {
            $v6Ranges = $v6Query->get()
                ->map(function (stdClass $row) {
                    $sanitized = Type\shape([
                        'start_ip' => Type\non_empty_string(),
                        'end_ip' => Type\non_empty_string(),
                        'coordinates' => Type\non_empty_string(),
                    ])->coerce($row);

                    return new IpRange(
                        $sanitized['start_ip'],
                        $sanitized['end_ip'],
                        $this->createCoordinateObject($sanitized['coordinates']),
                        IpType::IPv6,
                    );
                });
        }

        return $v4Ranges->merge($v6Ranges);
    }

    public function supports(Provider $provider): bool
    {
        return $provider === Provider::Maxmind;
    }

    private function createCoordinateObject($coordinatesString): Coordinates
    {
        $coordinates = Str::of($coordinatesString)->after('POINT(')->before(')')
            ->explode(' ')
            ->map(fn(string $coordinate) => (float)$coordinate)
            ->toArray();

        $sanitizedCoordinates = Type\shape([
            0 => Type\float(),
            1 => Type\float(),
        ])->coerce($coordinates);

        return new Coordinates(
            $sanitizedCoordinates[0],
            $sanitizedCoordinates[1],
        );
    }
}
