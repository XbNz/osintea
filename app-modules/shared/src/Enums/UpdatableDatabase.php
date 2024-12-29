<?php

declare(strict_types=1);

namespace XbNz\Shared\Enums;

enum UpdatableDatabase: string
{
    case RouteViewsAsnMmdbIpv4 = 'route_views_asn_mmdb_ipv4';
    case RouteViewsAsnMmdbIpv6 = 'route_views_asn_mmdb_ipv6';

    case MaxmindGeolite2CityMmdbIpv4 = 'maxmind_geolite2_city_mmdb_ipv4';
    case MaxmindGeolite2CityMmdbIpv6 = 'maxmind_geolite2_city_mmdb_ipv6';
    case RouteViewsAsnUnifiedSqlite = 'route_views_asn_unified_sqlite';

    case MaxmindGeoLite2CityUnifiedSqlite = 'maxmind_geo_lite2_city_unified_sqlite';

    case Fake = 'fake';

    public function friendlyName(): string
    {
        return match ($this) {
            self::RouteViewsAsnMmdbIpv4 => 'Route Views ASN MMDB IPv4',
            self::RouteViewsAsnMmdbIpv6 => 'Route Views ASN MMDB IPv6',
            self::MaxmindGeolite2CityMmdbIpv4 => 'Maxmind GeoLite2 City MMDB IPv4',
            self::MaxmindGeolite2CityMmdbIpv6 => 'Maxmind GeoLite2 City MMDB IPv6',
            self::RouteViewsAsnUnifiedSqlite => 'Route Views ASN Unified SQLite',
            self::MaxmindGeoLite2CityUnifiedSqlite => 'Maxmind GeoLite2 City Unified SQLite',
            self::Fake => 'Fake',
        };
    }

    public function canBeUsedInProduction(): bool
    {
        return $this !== self::Fake;
    }
}
