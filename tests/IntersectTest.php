<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class IntersectTest extends TestCase
{
    public function test_intersect_list_auto_mode(): void
    {
        $input = [1, 2, 3, 4];
        $other = [2, 4, 5, 6];

        $result = intersect($input, $other);

        $this->assertSame([2, 4], $result);
    }

    public function test_intersect_list_explicit_list_mode(): void
    {
        $input = [1, 2, 3, 4];
        $other = [3, 4, 5];

        $result = intersect($input, $other, Mode::MODE_LIST);

        $this->assertSame([3, 4], $result);
    }

    public function test_intersect_assoc_auto_mode(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];
        $other = [
            'dave' => 20,
            'eve'  => 23,
        ];

        $result = intersect($input, $other);

        $this->assertSame(['alice' => 20, 'carol' => 23], $result);
    }

    public function test_intersect_assoc_explicit_assoc_mode(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];
        $other = [
            'x' => 2,
            'y' => 3,
        ];

        $result = intersect($input, $other, Mode::MODE_ASSOC);

        $this->assertSame(['b' => 2, 'c' => 3], $result);
    }

    public function test_intersect_empty_input_returns_empty(): void
    {
        /** @var list<int> $input */
        $input = [];
        $other = [1, 2, 3];

        $result = intersect($input, $other);

        $this->assertSame([], $result);
    }

    public function test_intersect_empty_other_returns_empty(): void
    {
        $input = [1, 2, 3];
        /** @var list<int> $other */
        $other = [];

        $result = intersect($input, $other);

        $this->assertSame([], $result);
    }

    public function test_intersect_no_common_elements(): void
    {
        $input = [1, 2, 3];
        $other = [4, 5, 6];

        $result = intersect($input, $other);

        $this->assertSame([], $result);
    }

    public function test_intersect_all_common_elements(): void
    {
        $input = [1, 2, 3];
        $other = [1, 2, 3];

        $result = intersect($input, $other);

        $this->assertSame([1, 2, 3], $result);
    }

    public function test_intersect_list_reindexes_result(): void
    {
        $input = [10, 20, 30, 40];
        $other = [20, 40];

        $result = intersect($input, $other, Mode::MODE_LIST);

        // array_intersect は元のキー（1, 3）を保持するが、LIST モードでは 0, 1 にリインデックスされる
        $this->assertSame([20, 40], $result);
        $this->assertSame([0, 1], array_keys($result));
    }

    public function test_intersect_with_string_values(): void
    {
        $input = ['apple', 'banana', 'cherry'];
        $other = ['banana', 'cherry', 'date'];

        $result = intersect($input, $other);

        $this->assertSame(['banana', 'cherry'], $result);
    }
}
