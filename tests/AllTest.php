<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class AllTest extends TestCase
{
    public function testAllOnEmptyArrayReturnsTrueAndDoesNotCallCallback(): void
    {
        $input     = [];
        $callCount = 0;

        $result = all($input, function () use (&$callCount): bool {
            $callCount++;

            return true;
        });

        $this->assertTrue($result);
        $this->assertSame(0, $callCount);
    }

    public function testAllReturnsTrueWhenAllElementsMatch(): void
    {
        // @phpstan-ignore identical.alwaysTrue
        $this->assertTrue(all([2, 4, 6, 8], fn (int $v): bool => $v % 2 === 0));
    }

    public function testAllReturnsFalseWhenNoElementMatches(): void
    {
        // @phpstan-ignore identical.alwaysFalse
        $this->assertFalse(all([1, 3, 5], fn (int $v): bool => $v % 2 === 0));
    }

    public function testAllReturnsFalseWithMixedElements(): void
    {
        $this->assertFalse(all([2, 4, 5, 6], fn (int $v): bool => $v % 2 === 0));
    }

    public function testAllWorksWithListInputAndUsesIntKey(): void
    {
        $input = [10, 20, 30];

        $result = all($input, fn (int $v, int $k): bool => $v === ($k + 1) * 10);

        $this->assertTrue($result);
    }

    public function testAllWorksWithAssocArrayAndUsesStringKey(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = all($input, function (int $v, string $k): bool {
            return match ($k) {
                'a'     => $v === 1,
                'b'     => $v === 2,
                default => $v === 3,
            };
        });

        $this->assertTrue($result);
    }

    public function testAllShortCircuitsOnFirstFalse(): void
    {
        $input     = [1, 2, 3, 4];
        $callCount = 0;

        $result = all($input, function (int $v, int $k) use (&$callCount): bool {
            $callCount++;

            return $v !== 2;
        });

        $this->assertFalse($result);
        $this->assertSame(2, $callCount);
    }
}
