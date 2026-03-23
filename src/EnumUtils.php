<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils;

/**
 * Utility trait for backed enums providing common lookup, listing, and comparison helpers.
 *
 * @mixin \BackedEnum
 */
trait EnumUtils
{
    /**
     * Resolve an enum case by its name (case-insensitive).
     *
     * @throws \ValueError If no matching case is found.
     */
    public static function fromName(string $name): static
    {
        $case = static::tryFromName($name);

        if ($case === null) {
            throw new \ValueError(sprintf('"%s" is not a valid name for enum "%s"', $name, static::class));
        }

        return $case;
    }

    /**
     * Try to resolve an enum case by its name (case-insensitive).
     *
     * Returns null if no matching case is found.
     */
    public static function tryFromName(string $name): ?static
    {
        $lower = strtolower($name);

        foreach (static::cases() as $case) {
            if (strtolower($case->name) === $lower) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get all case names as a flat array.
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_map(
            static fn (self $case): string => $case->name,
            static::cases(),
        );
    }

    /**
     * Get all case values as a flat array.
     *
     * @return array<int, string|int>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $case): string|int => $case->value,
            static::cases(),
        );
    }

    /**
     * Get a random enum case.
     */
    public static function random(): static
    {
        $cases = static::cases();

        return $cases[array_rand($cases)];
    }

    /**
     * Filter enum cases using a custom predicate.
     *
     * The callable receives each case and should return true to include it.
     *
     * @param  callable(static): bool  $filter
     * @return array<int, static>
     */
    public static function casesWhere(callable $filter): array
    {
        return array_values(array_filter(
            static::cases(),
            static fn (self $case): bool => $filter($case),
        ));
    }

    /**
     * Check whether this case is among the given set of cases.
     */
    public function in(self ...$cases): bool
    {
        foreach ($cases as $case) {
            if ($this === $case) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build an array suitable for HTML select elements: [value => label].
     *
     * Labels are resolved from the Label attribute if present, otherwise the
     * case name is humanized (e.g. "InProgress" becomes "In Progress").
     *
     * @return array<string|int, string>
     */
    public static function toSelectArray(): array
    {
        $result = [];

        foreach (static::cases() as $case) {
            $result[$case->value] = EnumMeta::label($case);
        }

        return $result;
    }

    /**
     * Build an associative array of [name => value] for all cases.
     *
     * @return array<string, string|int>
     */
    public static function toArray(): array
    {
        $result = [];

        foreach (static::cases() as $case) {
            $result[$case->name] = $case->value;
        }

        return $result;
    }

    /**
     * Get the total number of cases in this enum.
     */
    public static function count(): int
    {
        return count(static::cases());
    }

    /**
     * Check whether this case is equal to another case of the same enum.
     */
    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
