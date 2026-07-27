<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class NoneTest extends TestCase
{
    public function testNoneOnEmptyArrayReturnsTrueAndDoesNotCallCallback(): void
    {
        $input     = [];
        $callCount = 0;

        $result = none($input, function () use (&$callCount): bool {
            $callCount++;

            return true;
        });

        $this->assertTrue($result);
        $this->assertSame(0, $callCount);
    }

    public function testNoneReturnsTrueWhenNoElementMatches(): void
    {
        // @phpstan-ignore identical.alwaysFalse
        $this->assertTrue(none([1, 3, 5], fn (int $v): bool => $v % 2 === 0));
    }

    public function testNoneReturnsFalseWhenAllElementsMatch(): void
    {
        // @phpstan-ignore identical.alwaysTrue
        $this->assertFalse(none([2, 4, 6], fn (int $v): bool => $v % 2 === 0));
    }

    public function testNoneReturnsFalseWithMixedElements(): void
    {
        $this->assertFalse(none([1, 2, 3], fn (int $v): bool => $v % 2 === 0));
    }

    public function testNoneWorksWithListInputAndUsesIntKey(): void
    {
        $input = [10, 20, 30];

        $result = none($input, fn (int $v, int $k): bool => $v !== ($k + 1) * 10);

        $this->assertTrue($result);
    }

    public function testNoneWorksWithAssocArrayAndUsesStringKey(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = none($input, function (int $v, string $k): bool {
            return match ($k) {
                'a'     => $v !== 1,
                'b'     => $v !== 2,
                default => $v !== 3,
            };
        });

        $this->assertTrue($result);
    }

    public function testNoneShortCircuitsOnFirstTrue(): void
    {
        $input     = [1, 2, 3, 4];
        $callCount = 0;

        $result = none($input, function (int $v, int $k) use (&$callCount): bool {
            $callCount++;

            return $v === 2;
        });

        $this->assertFalse($result);
        $this->assertSame(2, $callCount);
    }
}
