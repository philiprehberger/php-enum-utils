<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests;

use PhilipRehberger\EnumUtils\EnumCollection;
use PhilipRehberger\EnumUtils\Tests\Fixtures\IntPriority;
use PhilipRehberger\EnumUtils\Tests\Fixtures\StringStatus;
use PHPUnit\Framework\TestCase;

final class EnumCollectionTest extends TestCase
{
    public function test_collect_returns_enum_collection(): void
    {
        $collection = StringStatus::collect();

        $this->assertInstanceOf(EnumCollection::class, $collection);
        $this->assertCount(4, $collection);
    }

    public function test_filter_returns_matching_cases(): void
    {
        $result = StringStatus::collect()
            ->filter(static fn (StringStatus $case): bool => str_starts_with($case->name, 'C'));

        $this->assertSame([StringStatus::Completed, StringStatus::Cancelled], $result->toArray());
    }

    public function test_filter_returns_empty_collection_when_no_match(): void
    {
        $result = StringStatus::collect()
            ->filter(static fn (StringStatus $case): bool => false);

        $this->assertTrue($result->isEmpty());
        $this->assertSame(0, $result->count());
    }

    public function test_map_transforms_cases(): void
    {
        $result = StringStatus::collect()
            ->map(static fn (StringStatus $case): string => $case->value);

        $this->assertSame(['pending', 'in_progress', 'completed', 'cancelled'], $result);
    }

    public function test_first_returns_first_case(): void
    {
        $result = StringStatus::collect()->first();

        $this->assertSame(StringStatus::Pending, $result);
    }

    public function test_first_with_callback_returns_first_matching(): void
    {
        $result = StringStatus::collect()
            ->first(static fn (StringStatus $case): bool => str_starts_with($case->value, 'c'));

        $this->assertSame(StringStatus::Completed, $result);
    }

    public function test_first_returns_null_on_empty(): void
    {
        $result = StringStatus::collect()
            ->filter(static fn (StringStatus $case): bool => false)
            ->first();

        $this->assertNull($result);
    }

    public function test_first_with_callback_returns_null_when_no_match(): void
    {
        $result = StringStatus::collect()
            ->first(static fn (StringStatus $case): bool => false);

        $this->assertNull($result);
    }

    public function test_last_returns_last_case(): void
    {
        $result = StringStatus::collect()->last();

        $this->assertSame(StringStatus::Cancelled, $result);
    }

    public function test_last_with_callback_returns_last_matching(): void
    {
        $result = StringStatus::collect()
            ->last(static fn (StringStatus $case): bool => str_starts_with($case->name, 'C'));

        $this->assertSame(StringStatus::Cancelled, $result);
    }

    public function test_last_returns_null_on_empty(): void
    {
        $result = StringStatus::collect()
            ->filter(static fn (StringStatus $case): bool => false)
            ->last();

        $this->assertNull($result);
    }

    public function test_sort_by_sorts_cases(): void
    {
        $result = IntPriority::collect()
            ->sortBy(static fn (IntPriority $case): int => -$case->value)
            ->toArray();

        $this->assertSame(
            [IntPriority::Critical, IntPriority::High, IntPriority::Medium, IntPriority::Low],
            $result,
        );
    }

    public function test_group_by_groups_cases(): void
    {
        $result = IntPriority::collect()
            ->groupBy(static fn (IntPriority $case): string => $case->value >= 3 ? 'high' : 'low');

        $this->assertSame([IntPriority::Low, IntPriority::Medium], $result['low']);
        $this->assertSame([IntPriority::High, IntPriority::Critical], $result['high']);
    }

    public function test_partition_splits_cases(): void
    {
        [$matching, $nonMatching] = StringStatus::collect()
            ->partition(static fn (StringStatus $case): bool => $case->value === 'pending');

        $this->assertSame([StringStatus::Pending], $matching);
        $this->assertCount(3, $nonMatching);
    }

    public function test_values_returns_backing_values(): void
    {
        $result = IntPriority::collect()->values();

        $this->assertSame([1, 2, 3, 4], $result);
    }

    public function test_count_returns_correct_count(): void
    {
        $this->assertSame(4, StringStatus::collect()->count());
    }

    public function test_is_empty_and_is_not_empty(): void
    {
        $full = StringStatus::collect();
        $empty = StringStatus::collect()->filter(static fn (): bool => false);

        $this->assertFalse($full->isEmpty());
        $this->assertTrue($full->isNotEmpty());
        $this->assertTrue($empty->isEmpty());
        $this->assertFalse($empty->isNotEmpty());
    }

    public function test_collection_is_iterable(): void
    {
        $cases = [];

        foreach (StringStatus::collect() as $case) {
            $cases[] = $case;
        }

        $this->assertSame(StringStatus::cases(), $cases);
    }

    public function test_chained_operations(): void
    {
        $result = IntPriority::collect()
            ->filter(static fn (IntPriority $case): bool => $case->value >= 2)
            ->sortBy(static fn (IntPriority $case): int => -$case->value)
            ->toArray();

        $this->assertSame(
            [IntPriority::Critical, IntPriority::High, IntPriority::Medium],
            $result,
        );
    }
}
