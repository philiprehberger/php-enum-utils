<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Attributes;

use Attribute;

/**
 * Assign a longer description to an enum case.
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class Description
{
    /**
     * Create a new Description attribute instance.
     */
    public function __construct(
        public string $text,
    ) {}
}
