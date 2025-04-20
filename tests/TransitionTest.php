<?php

declare(strict_types=1);

namespace PhilipRehberger\EnumUtils\Tests;

use PhilipRehberger\EnumUtils\Tests\Fixtures\OrderStatus;
use PhilipRehberger\EnumUtils\Tests\Fixtures\StringStatus;
use PHPUnit\Framework\TestCase;

final class TransitionTest extends TestCase
{
    public function test_can_transition_to_allowed_target(): void
    {
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Processing));
        $this->assertTrue(OrderStatus::Pending->canTransitionTo(OrderStatus::Cancelled));
        $this->assertTrue(OrderStatus::Processing->canTransitionTo(OrderStatus::Shipped));
        $this->assertTrue(OrderStatus::Processing->canTransitionTo(OrderStatus::Cancelled));
        $this->assertTrue(OrderStatus::Shipped->canTransitionTo(OrderStatus::Delivered));
    }

    public function test_can_transition_to_disallowed_target(): void
    {
        $this->assertFalse(OrderStatus::Pending->canTransitionTo(OrderStatus::Shipped));
        $this->assertFalse(OrderStatus::Pending->canTransitionTo(OrderStatus::Delivered));
        $this->assertFalse(OrderStatus::Processing->canTransitionTo(OrderStatus::Pending));
        $this->assertFalse(OrderStatus::Shipped->canTransitionTo(OrderStatus::Cancelled));
    }

    public function test_allowed_transitions_returns_correct_list(): void
    {
        $transitions = OrderStatus::Pending->allowedTransitions();

        $this->assertCount(2, $transitions);
        $this->assertSame(OrderStatus::Processing, $transitions[0]);
        $this->assertSame(OrderStatus::Cancelled, $transitions[1]);
    }

    public function test_allowed_transitions_for_processing(): void
    {
        $transitions = OrderStatus::Processing->allowedTransitions();

        $this->assertCount(2, $transitions);
        $this->assertSame(OrderStatus::Shipped, $transitions[0]);
        $this->assertSame(OrderStatus::Cancelled, $transitions[1]);
    }

    public function test_allowed_transitions_for_shipped(): void
    {
        $transitions = OrderStatus::Shipped->allowedTransitions();

        $this->assertCount(1, $transitions);
        $this->assertSame(OrderStatus::Delivered, $transitions[0]);
    }

    public function test_enum_without_transitions_returns_empty(): void
    {
        $transitions = OrderStatus::Delivered->allowedTransitions();

        $this->assertSame([], $transitions);
    }

    public function test_can_transition_to_returns_false_without_attribute(): void
    {
        $this->assertFalse(OrderStatus::Delivered->canTransitionTo(OrderStatus::Pending));
        $this->assertFalse(OrderStatus::Cancelled->canTransitionTo(OrderStatus::Pending));
    }

    public function test_enum_without_any_transitions_defined(): void
    {
        // StringStatus has no AllowedTransitions attributes at all
        $this->assertSame([], StringStatus::Pending->allowedTransitions());
        $this->assertFalse(StringStatus::Pending->canTransitionTo(StringStatus::InProgress));
    }
}
