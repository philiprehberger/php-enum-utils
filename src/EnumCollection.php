<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * A fluent collection wrapper for enum cases.
 *
 * @template T of \BackedEnum
 *
 * @implements IteratorAggregate<int, T>
 */
final class EnumCollection implements Countable, IteratorAggregate
{
    /**
     * @param  array<int, T>  $cases
     */
    public function __construct(
        private readonly array $cases,
    ) {}

    /**
     * Filter cases using a predicate.
     *
     * @param  callable(T): bool  $callback
     * @return self<T>
     */
    public function filter(callable $callback): self
    {
        return new self(array_values(array_filter($this->cases, $callback)));
    }

    /**
     * Map each case to a new value.
     *
     * @template U
     *
     * @param  callable(T): U  $callback
     * @return array<int, U>
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->cases);
    }

    /**
     * Get the first case, optionally matching a predicate.
     *
     * @param  (callable(T): bool)|null  $callback
     * @return T|null
     */
    public function first(?callable $callback = null): mixed
    {
        if ($callback === null) {
            return $this->cases[0] ?? null;
        }

        foreach ($this->cases as $case) {
            if ($callback($case)) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get the last case, optionally matching a predicate.
     *
     * @param  (callable(T): bool)|null  $callback
     * @return T|null
     */
    public function last(?callable $callback = null): mixed
    {
        if ($callback === null) {
            return $this->cases !== [] ? $this->cases[count($this->cases) - 1] : null;
        }

        $result = null;

        foreach ($this->cases as $case) {
            if ($callback($case)) {
                $result = $case;
            }
        }

        return $result;
    }

    /**
     * Sort cases by a callback's return value.
     *
     * @param  callable(T): mixed  $callback
     * @return self<T>
     */
    public function sortBy(callable $callback): self
    {
        $cases = $this->cases;

        usort($cases, static function ($a, $b) use ($callback): int {
            return $callback($a) <=> $callback($b);
        });

        return new self($cases);
    }

    /**
     * Group cases by a callback's return value.
     *
     * @param  callable(T): (string|int)  $callback
     * @return array<string|int, array<int, T>>
     */
    public function groupBy(callable $callback): array
    {
        $groups = [];

        foreach ($this->cases as $case) {
            $key = $callback($case);
            $groups[$key][] = $case;
        }

        return $groups;
    }

    /**
     * Partition cases into two arrays based on a predicate.
     *
     * Returns a two-element array: [matching, non-matching].
     *
     * @param  callable(T): bool  $callback
     * @return array{0: array<int, T>, 1: array<int, T>}
     */
    public function partition(callable $callback): array
    {
        $matching = [];
        $nonMatching = [];

        foreach ($this->cases as $case) {
            if ($callback($case)) {
                $matching[] = $case;
            } else {
                $nonMatching[] = $case;
            }
        }

        return [$matching, $nonMatching];
    }

    /**
     * Get the backing values of all cases in the collection.
     *
     * @return array<int, string|int>
     */
    public function values(): array
    {
        return array_map(static fn ($case): string|int => $case->value, $this->cases);
    }

    /**
     * Get all cases as a plain array.
     *
     * @return array<int, T>
     */
    public function toArray(): array
    {
        return $this->cases;
    }

    /**
     * Get the number of cases in the collection.
     */
    public function count(): int
    {
        return count($this->cases);
    }

    /**
     * Check if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return $this->cases === [];
    }

    /**
     * Check if the collection is not empty.
     */
    public function isNotEmpty(): bool
    {
        return $this->cases !== [];
    }

    /**
     * Get an iterator for the collection.
     *
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->cases);
    }
}
