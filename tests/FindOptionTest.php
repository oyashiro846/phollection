<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class FindOptionTest extends TestCase
{
    public function testFindOptionOnEmptyArrayReturnsNullAndDoesNotCallCallback(): void
    {
        $input     = [];
        $callCount = 0;

        $result = find_option($input, function (mixed $v, int|string $k) use (&$callCount): bool {
            $callCount++;

            return true;
        });

        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $this->assertNull($result);
        $this->assertSame(0, $callCount);
    }

    public function testFindOptionReturnsNullWhenNotFound(): void
    {
        $input = [1, 2, 3];
        // @phpstan-ignore greater.alwaysFalse
        $result = find_option($input, fn (int $v): bool => $v > 10);

        $this->assertNull($result);
    }

    public function testFindOptionWithListReturnsFirstMatchingValue(): void
    {
        $input  = [10, 20, 30];
        $result = find_option($input, fn (int $v): bool => $v === 20);

        $this->assertSame(20, $result);
    }

    public function testFindOptionWithAssocReturnsFirstMatchingValue(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = find_option($input, fn (int $v): bool => $v === 2);

        $this->assertSame(2, $result);
    }

    public function testFindOptionReturnsFirstMatchWhenMultipleMatch(): void
    {
        $input  = [1, 2, 3, 2, 5];
        $result = find_option($input, fn (int $v): bool => $v === 2);

        $this->assertSame(2, $result);
    }

    public function testFindOptionShortCircuitsOnFirstMatch(): void
    {
        $input     = [1, 2, 3, 4];
        $callCount = 0;

        $result = find_option($input, function (int $v, int $k) use (&$callCount): bool {
            $callCount++;

            return $v === 2;
        });

        $this->assertSame(2, $result);
        $this->assertSame(2, $callCount);
    }

    public function testFindOptionWithListPassesIterationIndexToCallback(): void
    {
        $input = [10, 20, 30];
        $keys  = [];

        $result = find_option($input, function (int $v, int $k) use (&$keys): bool {
            $keys[] = $k;

            return $v === 30;
        });

        $this->assertSame(30, $result);
        $this->assertSame([0, 1, 2], $keys);
    }

    public function testFindOptionWithAssocPassesOriginalKeyToCallback(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];
        $keys  = [];

        $result = find_option($input, function (int $v, string $k) use (&$keys): bool {
            $keys[] = $k;

            return $v === 3;
        });

        $this->assertSame(3, $result);
        $this->assertSame(['a', 'b', 'c'], $keys);
    }

    public function testFindOptionMatchesNullValueAndIsIndistinguishableFromNotFound(): void
    {
        $input  = ['a' => 1, 'b' => null, 'c' => 3];
        $result = find_option($input, fn (mixed $v, string $k): bool => $k === 'b');

        $this->assertNull($result);
    }

    public function testFindOptionDoesNotMutateInput(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];
        $copy  = $input;

        find_option($input, fn (int $v): bool => $v === 2);

        $this->assertSame($copy, $input);
    }
}
