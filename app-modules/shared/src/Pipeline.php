<?php

declare(strict_types=1);

namespace XbNz\Shared;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;
use UnexpectedValueException;

final class Pipeline
{
    private bool $useTransaction = false;

    private mixed $passable;

    /**
     * @var array<int, mixed>
     */
    private array $pipes = [];

    public static function make(): self
    {
        return new self();
    }

    public function withTransaction(): self
    {
        $this->useTransaction = true;

        return $this;
    }

    public function send(mixed $passable): self
    {
        $this->passable = $passable;

        return $this;
    }

    public function through(mixed ...$pipes): self
    {
        $this->pipes = count($pipes) === 1 && is_array($pipes[0]) ? $pipes[0] : $pipes;

        return $this;
    }

    public function then(Closure $step): mixed
    {
        return $step($this->traversePipeline());
    }

    public function thenReturn(): mixed
    {
        return $this->then(fn (mixed $passable): mixed => $passable);
    }

    private function traversePipeline(): mixed
    {
        try {
            $this->startTransaction();

            $result = array_reduce($this->pipes, $this->executePipe(...), $this->passable);

            $this->commitTransaction();

            return $result;
        } catch (Throwable $e) {
            $this->undoTransaction();

            throw $e;
        }
    }

    private function executePipe(mixed $previousValue, mixed $pipe): mixed
    {
        $action = $pipe;

        if (is_string($pipe) && class_exists($pipe)) {
            $action = resolve($pipe);
        }

        if (is_callable($action)) {
            return $action($previousValue);
        }

        if (is_object($action) && method_exists($action, 'handle')) {
            return $action->handle($previousValue);
        }

        throw new UnexpectedValueException('Pipeline only accepts callables and class strings');
    }

    private function startTransaction(): void
    {
        if ( ! $this->useTransaction) {
            return;
        }

        DB::beginTransaction();
    }

    private function commitTransaction(): void
    {
        if ( ! $this->useTransaction) {
            return;
        }

        DB::commit();
    }

    private function undoTransaction(): void
    {
        if ( ! $this->useTransaction) {
            return;
        }

        DB::rollBack();
    }
}
