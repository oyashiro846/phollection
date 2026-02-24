<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    public function testMapListModeWithListInput(): void
    {
        $input = [1, 2, 3];

        $result = map(
            $input,
            fn (int $value, int $key): int => $value * 10 + $key,
            Mode::MODE_LIST,
        );

        $this->assertSame([10, 21, 32], $result);
    }

    public function testMapListModeDropsAssocKeys(): void
    {
        $input = ['a' => 1, 'b' => 2];

        $result = map(
            $input,
            fn (int $value, string $key): string => $key . ':' . $value,
            Mode::MODE_LIST,
        );

        $this->assertSame(['a:1', 'b:2'], $result);
    }

    public function testMapAssocModePreservesKeys(): void
    {
        $input = ['x' => 1, 'y' => 2];

        $result = map(
            $input,
            fn (int $value, string $key): string => $key . ':' . ($value * 2),
            Mode::MODE_ASSOC,
        );

        $this->assertSame(
            ['x' => 'x:2', 'y' => 'y:4'],
            $result,
        );
    }

    public function testMapAutoDetectsList(): void
    {
        $input = [1, 2];

        $result = map(
            $input,
            fn (int $v, int $k): int => $v * 2,
        );

        $this->assertSame([2, 4], $result);
    }

    public function testMapAutoDetectsAssoc(): void
    {
        $input = ['a' => 1, 'b' => 2];

        $result = map(
            $input,
            fn (int $v, string $k): string => $k . $v,
        );

        $this->assertSame(
            ['a' => 'a1', 'b' => 'b2'],
            $result,
        );
    }

    public function testMapOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $this->assertSame(
            [],
            map($input, fn ($v, $k) => $v, Mode::MODE_LIST),
        );
        $this->assertSame(
            [],
            map($input, fn ($v, $k) => $v, Mode::MODE_ASSOC),
        );
        $this->assertSame(
            [],
            map($input, fn ($v, $k) => $v),
        );
    }
}
