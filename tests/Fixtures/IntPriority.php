<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests\Fixtures;

use PhilipRehberger\EnumUtils\Attributes\Description;
use PhilipRehberger\EnumUtils\Attributes\Label;
use PhilipRehberger\EnumUtils\EnumUtils;

enum IntPriority: int
{
    use EnumUtils;

    #[Label('Low Priority')]
    #[Description('Can be addressed later')]
    case Low = 1;

    #[Label('Medium Priority')]
    case Medium = 2;

    #[Label('High Priority')]
    #[Description('Should be addressed soon')]
    case High = 3;

    case Critical = 4;
}
