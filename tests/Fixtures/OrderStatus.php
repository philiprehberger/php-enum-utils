<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests\Fixtures;

use PhilipRehberger\EnumUtils\Attributes\AllowedTransitions;
use PhilipRehberger\EnumUtils\EnumUtils;

enum OrderStatus: string
{
    use EnumUtils;

    #[AllowedTransitions(self::Processing, self::Cancelled)]
    case Pending = 'pending';

    #[AllowedTransitions(self::Shipped, self::Cancelled)]
    case Processing = 'processing';

    #[AllowedTransitions(self::Delivered)]
    case Shipped = 'shipped';

    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
