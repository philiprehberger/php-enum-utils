<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests\Fixtures;

use PhilipRehberger\EnumUtils\Attributes\Description;
use PhilipRehberger\EnumUtils\Attributes\Label;
use PhilipRehberger\EnumUtils\EnumUtils;

enum StringStatus: string
{
    use EnumUtils;

    #[Label('Pending Review')]
    #[Description('The item is waiting for review')]
    case Pending = 'pending';

    #[Label('In Progress')]
    #[Description('The item is currently being worked on')]
    case InProgress = 'in_progress';

    #[Label('Completed')]
    case Completed = 'completed';

    case Cancelled = 'cancelled';
}
