<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Attributes;

use Attribute;

/**
 * Assign a human-readable label to an enum case.
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class Label
{
    /**
     * Create a new Label attribute instance.
     */
    public function __construct(
        public string $text,
    ) {}
}
