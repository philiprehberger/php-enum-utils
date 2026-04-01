<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Attributes;

use Attribute;
use UnitEnum;

/**
 * Define allowed state transitions for an enum case.
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
final readonly class AllowedTransitions
{
    /** @var array<int, UnitEnum> */
    public array $targets;

    /**
     * Create a new AllowedTransitions attribute instance.
     */
    public function __construct(UnitEnum ...$targets)
    {
        $this->targets = $targets;
    }
}
