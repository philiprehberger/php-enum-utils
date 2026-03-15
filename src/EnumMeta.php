<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils;

use BackedEnum;
use PhilipRehberger\EnumUtils\Attributes\Description;
use PhilipRehberger\EnumUtils\Attributes\Label;
use ReflectionEnumUnitCase;

/**
 * Static helper that reads Label and Description attributes from enum cases.
 */
final class EnumMeta
{
    /**
     * Get the label for a given enum case.
     *
     * Returns the Label attribute text if present, otherwise humanizes the case name
     * by inserting spaces before uppercase letters (e.g. "InProgress" becomes "In Progress").
     */
    public static function label(BackedEnum $case): string
    {
        $reflection = new ReflectionEnumUnitCase($case::class, $case->name);
        $attributes = $reflection->getAttributes(Label::class);

        if ($attributes !== []) {
            return $attributes[0]->newInstance()->text;
        }

        // Fallback: humanize the case name
        return self::humanize($case->name);
    }

    /**
     * Get the description for a given enum case.
     *
     * Returns null if no Description attribute is present.
     */
    public static function description(BackedEnum $case): ?string
    {
        $reflection = new ReflectionEnumUnitCase($case::class, $case->name);
        $attributes = $reflection->getAttributes(Description::class);

        if ($attributes !== []) {
            return $attributes[0]->newInstance()->text;
        }

        return null;
    }

    /**
     * Get all labels for every case in the given enum class, keyed by value.
     *
     * @param  class-string<BackedEnum>  $enumClass
     * @return array<string|int, string>
     */
    public static function labels(string $enumClass): array
    {
        $result = [];

        foreach ($enumClass::cases() as $case) {
            $result[$case->value] = self::label($case);
        }

        return $result;
    }

    /**
     * Convert a PascalCase or UPPER_CASE name into a human-readable string.
     */
    private static function humanize(string $name): string
    {
        // Replace underscores with spaces
        $name = str_replace('_', ' ', $name);

        // Insert a space before each uppercase letter that follows a lowercase letter
        $name = (string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $name);

        return ucfirst($name);
    }
}
