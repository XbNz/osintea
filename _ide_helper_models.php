<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace XbNz\Asn\Model{
/**
 * 
 *
 * @property int $id
 * @property int $ip_address_id
 * @property string $organization
 * @property int $as_number
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \XbNz\Asn\Model\TFactory|null $use_factory
 * @property-read \XbNz\Ip\Models\IpAddress $ipAddress
 * @method static \XbNz\Asn\Database\Factories\AsnFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereAsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereIpAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asn whereOrganization($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperAsn {}
}

namespace XbNz\Ip\Models{
/**
 * 
 *
 * @property int $id
 * @property string $ip
 * @property \XbNz\Shared\Enums\IpType|null $type
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \XbNz\Asn\Model\Asn|null $asn
 * @property-read \XbNz\Location\Models\Coordinates|null $coordinates
 * @property-read \XbNz\Ip\Models\TFactory|null $use_factory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \XbNz\Ping\Models\PingSequence> $pingSequences
 * @property-read int|null $ping_sequences_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \XbNz\Port\Models\Port> $ports
 * @property-read int|null $ports_count
 * @method static \XbNz\Ip\Database\Factories\IpAddressFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IpAddress whereType($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperIpAddress {}
}

namespace XbNz\Location\Models{
/**
 * 
 *
 * @property int $id
 * @property string $coordinates
 * @property int $ip_address_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \XbNz\Location\Models\TFactory|null $use_factory
 * @property-read \XbNz\Ip\Models\IpAddress $ipAddress
 * @method static \XbNz\Location\Database\Factories\CoordinatesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coordinates whereIpAddressId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperCoordinates {}
}

namespace XbNz\Ping\Models{
/**
 * 
 *
 * @property int $id
 * @property int $ip_address_id
 * @property float|null $round_trip_time
 * @property bool $loss
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \XbNz\Ping\Models\TFactory|null $use_factory
 * @property-read \XbNz\Ip\Models\IpAddress $ipAddress
 * @method static \XbNz\Ping\Database\Factories\PingSequenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence whereIpAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence whereLoss($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PingSequence whereRoundTripTime($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperPingSequence {}
}

namespace XbNz\Port\Models{
/**
 * 
 *
 * @property int $id
 * @property int $ip_address_id
 * @property \XbNz\Shared\Enums\ProtocolType $protocol
 * @property int $port
 * @property \XbNz\Shared\Enums\PortState $state
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \XbNz\Port\Models\TFactory|null $use_factory
 * @property-read \XbNz\Ip\Models\IpAddress $ipAddress
 * @method static \XbNz\Port\Database\Factories\PortFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port whereIpAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Port whereState($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperPort {}
}

namespace XbNz\Preferences\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $size
 * @property float $backoff
 * @property int $count
 * @property int $ttl
 * @property int $interval
 * @property int $interval_per_target
 * @property string $type_of_service
 * @property int $retries
 * @property int $timeout
 * @property bool $dont_fragment
 * @property bool $send_random_data
 * @property bool $enabled
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \XbNz\Preferences\Models\TFactory|null $use_factory
 * @method static \XbNz\Preferences\Database\Factories\FpingPreferencesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereBackoff($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereDontFragment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereIntervalPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereRetries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereSendRandomData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereTtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereTypeOfService($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FpingPreferences whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperFpingPreferences {}
}

namespace XbNz\Preferences\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $rate
 * @property string|null $adapter
 * @property int $retries
 * @property int $ttl
 * @property bool $enabled
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \XbNz\Preferences\Models\TFactory|null $use_factory
 * @method static \XbNz\Preferences\Database\Factories\MasscanPreferencesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereAdapter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereRetries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereTtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MasscanPreferences whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperMasscanPreferences {}
}

