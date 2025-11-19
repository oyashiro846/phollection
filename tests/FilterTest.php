<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function test_filter_list_auto_mode(): void
    {
        $input = [1, 2, 3, 4];

        $result = Arrays::filter(
            $input,
            fn (int $v): bool => $v % 2 === 0,
        );

        $this->assertSame([2, 4], $result);
    }

    public function test_filter_list_explicit_list_mode(): void
    {
        $input = [1, 2, 3, 4];

        $result = Arrays::filter(
            $input,
            fn (int $v): bool => $v > 2,
            Mode::MODE_LIST,
        );

        $this->assertSame([3, 4], $result);
    }

    public function test_filter_assoc_auto_mode(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = Arrays::filter(
            $input,
            fn (int $age, string $name): bool => $age >= 20,
        );

        $this->assertSame(['alice' => 20, 'carol' => 23], $result);
    }

    public function test_filter_assoc_explicit_assoc_mode(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = Arrays::filter(
            $input,
            fn (int $age): bool => $age >= 18,
            Mode::MODE_ASSOC,
        );

        $this->assertSame(['alice' => 20], $result);
    }

    public function test_filter_list_with_key_aware_callback(): void
    {
        $input = [10, 20, 30];

        $result = Arrays::filter(
            $input,
            fn (int $value, int $index): bool => $index % 2 === 0,
            Mode::MODE_LIST,
        );

        $this->assertSame([10, 30], $result);
    }
}
