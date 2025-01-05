<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use GeoJson\Feature\Feature;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon;
use GeoJson\Geometry\Polygon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Psl\Type;
use Webmozart\Assert\Assert;

final class FilterPolygon
{
    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->polygonFilter->canBeApplied() === false) {
            return $transporter;
        }

        $multiPolygonStatement = Collection::make($transporter->polygonFilter->geoJsons)
            ->map(fn (array $geoJson) => GeoJson::jsonUnserialize($geoJson))
            ->map(fn (FeatureCollection $featureCollection) => $featureCollection->getFeatures())
            ->flatten(1)
            ->filter(fn (Feature $feature) => $feature->getGeometry() instanceof Polygon)
            ->map(fn (Feature $feature) => $feature->getGeometry())
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

        $transporter->query
            ->where(function (Builder $query) use ($multiPolygonStatement): void {
                $query->whereHas('coordinates', function (Builder $query) use ($multiPolygonStatement): void {
                    $query->whereRaw("ST_Contains(ST_GeomFromText('MULTIPOLYGON({$multiPolygonStatement})'), coordinates)");
                });
            });

        return $transporter;
    }
}
