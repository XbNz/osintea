<?php

declare(strict_types=1);

namespace XbNz\Ip\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webmozart\Assert\Assert;

final class StringResolvesToIpAddressRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) === false) {
            $fail('The :attribute must be a string');

            return;
        }

        Assert::string($value);

        $expectedIp = gethostbyname($value);

        if (filter_var($expectedIp, FILTER_VALIDATE_IP) === false) {
            $fail('The :attribute must resolve to a valid IP address');
        }
    }
}
