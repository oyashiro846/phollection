<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class AnyTest extends TestCase
{
    public function testAnyReturnsTrueWhenSomeElementMatches(): void
    {
        $this->assertTrue(any([1, 2, 3, 4], fn (int $v): bool => $v % 2 === 0));
    }

    public function testAnyReturnsFalseWhenNoElementMatches(): void
    {
        // @phpstan-ignore  identical.alwaysFalse
        $this->assertFalse(any([1, 3, 5], fn (int $v): bool => $v % 2 === 0));
    }

    public function testAnyWorksWithAssocArrayAndUsesKey(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = any($input, function (int $v, string $k): bool {
            return $k === 'b' && $v === 2;
        });

        $this->assertTrue($result);
    }

    public function testAnyShortCircuitsOnFirstTrue(): void
    {
        $input     = [1, 2, 3, 4];
        $callCount = 0;

        $result = any($input, function (int $v, int $k) use (&$callCount): bool {
            $callCount++;

            return $v === 3;
        });

        $this->assertTrue($result);
        $this->assertSame(3, $callCount);
    }

    public function testAnyOnEmptyArrayReturnsFalseAndDoesNotCallCallback(): void
    {
        $input     = [];
        $callCount = 0;

        $result = any($input, function () use (&$callCount): bool {
            $callCount++;

            return true;
        });

        $this->assertFalse($result);
        $this->assertSame(0, $callCount);
    }
}
