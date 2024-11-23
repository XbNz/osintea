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


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	final class IdeHelperUser {}
}

namespace XbNz\Ip\Models{
/**
 * 
 *
 * @property int $id
 * @property string $ip
 * @property \XbNz\Shared\ValueObjects\IpType|null $type
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \XbNz\Ping\Models\PingSequence> $pingSequences
 * @property-read int|null $ping_sequences_count
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

namespace XbNz\Ping\Models{
/**
 * 
 *
 * @property int $id
 * @property int $ip_address_id
 * @property float|null $round_trip_time
 * @property bool $loss
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \XbNz\Ip\Models\IpAddress|null $ipAddress
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

