<?php

declare(strict_types=1);

namespace XbNz\Ip\Steps\ManipulateIpAddressQuery;

use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon;
use Webmozart\Assert\Assert;

final class FilterPolygon
{
    public function handle(Transporter $transporter): Transporter
    {
        if ($transporter->polygonFilter->canBeApplied() === false) {
            return $transporter;
        }

        Assert::isInstanceOf($geoJsonObject = GeoJson::jsonUnserialize($transporter->polygonFilter->geoJson), FeatureCollection::class);
        Assert::isInstanceOf($multiPolygon = $geoJsonObject->getFeatures()[0]->getGeometry(), MultiPolygon::class);

        $transporter->query;

        return $transporter;
    }
}
