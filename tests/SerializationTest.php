<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests;

use PhilipRehberger\EnumUtils\Tests\Fixtures\IntPriority;
use PhilipRehberger\EnumUtils\Tests\Fixtures\StringStatus;
use PHPUnit\Framework\TestCase;

final class SerializationTest extends TestCase
{
    public function test_to_json_output_format(): void
    {
        $json = StringStatus::toJson();
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(4, $data);

        // First case has label and description attributes
        $this->assertSame('Pending', $data[0]['name']);
        $this->assertSame('pending', $data[0]['value']);
        $this->assertSame('Pending Review', $data[0]['label']);
        $this->assertSame('The item is waiting for review', $data[0]['description']);

        // Case with label but no description
        $this->assertSame('InProgress', $data[1]['name']);
        $this->assertSame('in_progress', $data[1]['value']);
        $this->assertSame('In Progress', $data[1]['label']);
        $this->assertArrayNotHasKey('description', $data[2]);

        // Case without label attribute (humanized name matches case name)
        $this->assertSame('Cancelled', $data[3]['name']);
        $this->assertSame('cancelled', $data[3]['value']);
        $this->assertArrayNotHasKey('label', $data[3]);
    }

    public function test_to_json_with_int_backed_enum(): void
    {
        $json = IntPriority::toJson();
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(1, $data[0]['value']);
        $this->assertSame('Low Priority', $data[0]['label']);
    }

    public function test_from_json_round_trip(): void
    {
        $json = StringStatus::toJson();
        $cases = StringStatus::fromJson($json);

        $this->assertSame(StringStatus::cases(), $cases);
    }

    public function test_from_json_round_trip_with_int_backed_enum(): void
    {
        $json = IntPriority::toJson();
        $cases = IntPriority::fromJson($json);

        $this->assertSame(IntPriority::cases(), $cases);
    }

    public function test_from_json_throws_on_invalid_json(): void
    {
        $this->expectException(\JsonException::class);

        StringStatus::fromJson('not-json');
    }

    public function test_to_map_returns_value_to_label(): void
    {
        $map = StringStatus::toMap();

        $expected = [
            'pending' => 'Pending Review',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $this->assertSame($expected, $map);
    }

    public function test_to_map_with_int_backed_enum(): void
    {
        $map = IntPriority::toMap();

        $expected = [
            1 => 'Low Priority',
            2 => 'Medium Priority',
            3 => 'High Priority',
            4 => 'Critical',
        ];

        $this->assertSame($expected, $map);
    }
}
