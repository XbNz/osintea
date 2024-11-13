<?php

declare(strict_types=1);

namespace XbNz\Shared\Actions;

use ReflectionClass;
use XbNz\Shared\Attributes\ListensTo;

final class ResolveListenersAction
{
    /**
     * @param  class-string  $subscriberClass
     * @return array<int, array{0: string, 1: array{0: class-string, 1: string}}>
     */
    public function handle(string $subscriberClass): array
    {
        $reflectionClass = new ReflectionClass($subscriberClass);

        $listeners = [];

        foreach ($reflectionClass->getMethods() as $method) {
            $attributes = $method->getAttributes(ListensTo::class);

            foreach ($attributes as $attribute) {
                $listener = $attribute->newInstance();

                $listeners[] = [
                    $listener->event,
                    [$subscriberClass, $method->getName()],
                ];
            }
        }

        return $listeners;
    }
}
