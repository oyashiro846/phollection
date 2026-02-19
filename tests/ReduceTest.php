<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class ReduceTest extends TestCase
{
    public function testReduceWithoutInitialOnNonEmptyArray(): void
    {
        $input = [1, 2, 3, 4];

        $sum = Arrays::reduce(
            $input,
            function (?int $carry, int $item): int {
                return ($carry ?? 0) + $item;
            },
        );

        $this->assertSame(10, $sum);
    }

    public function testReduceWithInitial(): void
    {
        $input = [1, 2, 3];

        $sum = Arrays::reduce(
            $input,
            function (int $carry, int $item): int {
                return $carry + $item;
            },
            initial: 10,
        );

        $this->assertSame(16, $sum);
    }

    public function testReduceEmptyArrayWithInitialReturnsInitial(): void
    {
        $input = [];

        $result = Arrays::reduce(
            $input,
            function ($carry, $item) {
                return $carry + $item;
            },
            initial: 42,
        );

        $this->assertSame(42, $result);
    }

    public function testReduceEmptyArrayWithoutInitialReturnsNull(): void
    {
        $input = [];

        $result = Arrays::reduce(
            $input,
            function ($carry, $item) {
                return $carry + $item;
            },
        );

        $this->assertNull($result);
    }

    public function testReduceIgnoresArrayKeys(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = Arrays::reduce(
            $input,
            function (int $carry, int $item): int {
                return $carry + $item;
            },
            initial: 0,
        );

        $this->assertSame(6, $result);
    }
}
