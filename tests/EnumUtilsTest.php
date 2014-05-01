<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests;

use PhilipRehberger\EnumUtils\EnumMeta;
use PhilipRehberger\EnumUtils\Tests\Fixtures\IntPriority;
use PhilipRehberger\EnumUtils\Tests\Fixtures\StringStatus;
use PHPUnit\Framework\TestCase;

final class EnumUtilsTest extends TestCase
{
    public function test_from_name_with_exact_match(): void
    {
        $this->assertSame(StringStatus::Pending, StringStatus::fromName('Pending'));
    }

    public function test_from_name_with_case_insensitive_match(): void
    {
        $this->assertSame(StringStatus::Pending, StringStatus::fromName('pending'));
        $this->assertSame(StringStatus::InProgress, StringStatus::fromName('INPROGRESS'));
    }

    public function test_from_name_throws_on_invalid_name(): void
    {
        $this->expectException(\ValueError::class);

        StringStatus::fromName('NonExistent');
    }

    public function test_try_from_name_returns_null_on_invalid(): void
    {
        $this->assertNull(StringStatus::tryFromName('NonExistent'));
    }

    public function test_names_returns_correct_array(): void
    {
        $this->assertSame(
            ['Pending', 'InProgress', 'Completed', 'Cancelled'],
            StringStatus::names(),
        );
    }

    public function test_values_returns_correct_array(): void
    {
        $this->assertSame(
            ['pending', 'in_progress', 'completed', 'cancelled'],
            StringStatus::values(),
        );
    }

    public function test_random_returns_a_valid_case(): void
    {
        $case = StringStatus::random();

        $this->assertContains($case, StringStatus::cases());
    }

    public function test_to_select_array_maps_value_to_label(): void
    {
        $expected = [
            'pending' => 'Pending Review',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $this->assertSame($expected, StringStatus::toSelectArray());
    }

    public function test_to_array_maps_name_to_value(): void
    {
        $expected = [
            'Pending' => 'pending',
            'InProgress' => 'in_progress',
            'Completed' => 'completed',
            'Cancelled' => 'cancelled',
        ];

        $this->assertSame($expected, StringStatus::toArray());
    }

    public function test_count_is_correct(): void
    {
        $this->assertSame(4, StringStatus::count());
        $this->assertSame(4, IntPriority::count());
    }

    public function test_equals_comparison(): void
    {
        $this->assertTrue(StringStatus::Pending->equals(StringStatus::Pending));
        $this->assertFalse(StringStatus::Pending->equals(StringStatus::Completed));
    }

    public function test_label_attribute_read_correctly(): void
    {
        $this->assertSame('Pending Review', EnumMeta::label(StringStatus::Pending));
        $this->assertSame('In Progress', EnumMeta::label(StringStatus::InProgress));
    }

    public function test_description_attribute_read_correctly(): void
    {
        $this->assertSame('The item is waiting for review', EnumMeta::description(StringStatus::Pending));
        $this->assertNull(EnumMeta::description(StringStatus::Completed));
    }

    public function test_missing_label_falls_back_to_humanized_case_name(): void
    {
        $this->assertSame('Cancelled', EnumMeta::label(StringStatus::Cancelled));
    }

    public function test_works_with_int_backed_enums(): void
    {
        $this->assertSame(IntPriority::High, IntPriority::fromName('High'));
        $this->assertSame([1, 2, 3, 4], IntPriority::values());
        $this->assertSame(['Low' => 1, 'Medium' => 2, 'High' => 3, 'Critical' => 4], IntPriority::toArray());

        $expected = [
            1 => 'Low Priority',
            2 => 'Medium Priority',
            3 => 'High Priority',
            4 => 'Critical',
        ];
        $this->assertSame($expected, IntPriority::toSelectArray());
    }
}
