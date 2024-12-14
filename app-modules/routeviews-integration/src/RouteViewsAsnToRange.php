<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Psl\Type;
use stdClass;
use Webmozart\Assert\Assert;
use XbNz\Asn\Contracts\AsnToRangeInterface;
use XbNz\Asn\Enums\Provider;
use XbNz\Asn\ValueObject\Asn;
use XbNz\Asn\ValueObject\IpRange;
use XbNz\Shared\ValueObjects\IpType;

final class RouteViewsAsnToRange implements AsnToRangeInterface
{
    private int $ipTypeMask = AsnToRangeInterface::FILTER_IPV4;

    private ?string $organization;

    private ?int $asNumber;

    public function __construct(
        private readonly DatabaseManager $database,
    ) {}

    public function filterIpType(int $filterMask): self
    {
        $this->ipTypeMask = $filterMask;

        return $this;
    }

    public function organization(string $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function asNumber(int $asNumber): self
    {
        $this->asNumber = $asNumber;

        return $this;
    }

    /**
     * @return Collection<int, IpRange>
     */
    public function execute(): Collection
    {
        $this->ensureAtLeastOneIdentifierProvided();

        $requestedIpType = match ($this->ipTypeMask) {
            AsnToRangeInterface::FILTER_IPV4 => [IpType::IPv4],
            AsnToRangeInterface::FILTER_IPV6 => [IpType::IPv6],
            AsnToRangeInterface::FILTER_IPV4 | AsnToRangeInterface::FILTER_IPV6 => [IpType::IPv4, IpType::IPv6],
            default => throw new InvalidArgumentException('Need at least one IP type to filter by.'),
        };

        $v4Query = $this->database
            ->table('route_views_v4_asns')
            ->when(isset($this->organization), fn (Builder $query, $organization) => $query->where('organization', 'like', "%{$this->organization}%"))
            ->when(isset($this->asNumber), fn (Builder $query, $asNumber) => $query->where('asn', $this->asNumber));

        $v6Query = $this->database
            ->table('route_views_v6_asns')
            ->when(isset($this->organization), fn (Builder $query, $organization) => $query->where('organization', 'like', "%{$this->organization}%"))
            ->when(isset($this->asNumber), fn (Builder $query, $asNumber) => $query->where('asn', $this->asNumber));

        $v4Ranges = Collection::make();
        $v6Ranges = Collection::make();

        if (in_array(IpType::IPv4, $requestedIpType, true)) {
            $v4Ranges = $v4Query->get()
                ->map(function (stdClass $row) {
                    $sanitized = Type\shape([
                        'start_ip' => Type\non_empty_string(),
                        'end_ip' => Type\non_empty_string(),
                        'asn' => Type\positive_int(),
                        'organization' => Type\non_empty_string(),
                    ])->coerce($row);

                    return new IpRange(
                        $sanitized['start_ip'],
                        $sanitized['end_ip'],
                        new Asn(
                            $sanitized['organization'],
                            $sanitized['asn']
                        ),
                        IpType::IPv4
                    );
                });
        }

        if (in_array(IpType::IPv6, $requestedIpType, true)) {
            $v6Ranges = $v6Query->get()
                ->map(function (stdClass $row) {
                    $sanitized = Type\shape([
                        'start_ip' => Type\non_empty_string(),
                        'end_ip' => Type\non_empty_string(),
                        'asn' => Type\positive_int(),
                        'organization' => Type\non_empty_string(),
                    ])->coerce($row);

                    return new IpRange(
                        $sanitized['start_ip'],
                        $sanitized['end_ip'],
                        new Asn(
                            $sanitized['organization'],
                            $sanitized['asn']
                        ),
                        IpType::IPv6
                    );
                });
        }

        return $v4Ranges->merge($v6Ranges);
    }

    private function ensureAtLeastOneIdentifierProvided(): void
    {
        Assert::true(
            isset($this->organization) || isset($this->asNumber),
            'Need at least one identifier to search by'
        );
    }

    public function all(): Builder
    {
        $allV4Query = $this->database
            ->table('route_views_v4_asns')
            ->when(isset($this->organization), fn (Builder $query, $organization) => $query->where('organization', 'LIKE', "%{$this->organization}%"))
            ->when(isset($this->asNumber), fn (Builder $query, $asNumber) => $query->where('asn', $this->asNumber))
            ->select(['asn', 'organization'])
            ->groupBy('asn');

        $allV6Query = $this->database
            ->table('route_views_v6_asns')
            ->addSelect('route_views_v6_asns.asn')
            ->addSelect('route_views_v6_asns.organization')
            ->leftJoin('route_views_v4_asns', 'route_views_v4_asns.asn', '=', 'route_views_v6_asns.asn')
            ->whereNull('route_views_v4_asns.asn')
            ->when(isset($this->organization), fn (Builder $query, $organization) => $query->where('route_views_v6_asns.organization', 'LIKE', "%{$this->organization}%"))
            ->when(isset($this->asNumber), fn (Builder $query, $asNumber) => $query->where('route_views_v6_asns.asn', $this->asNumber))
            ->groupBy('route_views_v6_asns.asn');

        return $allV4Query->unionAll($allV6Query);
    }

    public function supports(Provider $provider): bool
    {
        return $provider === Provider::RouteViews;
    }
}
