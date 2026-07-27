<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class FlatMapTest extends TestCase
{
    public function testFlatMapOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = flat_map(
            $input,
            fn ($value, $key): array => [$value],
        );

        $this->assertSame([], $result);
    }

    public function testFlatMapWhenEveryCallbackReturnsEmptyReturnsEmpty(): void
    {
        $input = [1, 2, 3];

        $result = flat_map(
            $input,
            fn (int $value, int $index): array => [],
        );

        $this->assertSame([], $result);
    }

    public function testFlatMapWhenSomeCallbacksReturnEmptyDropsThem(): void
    {
        $input = [1, 2, 3, 4];

        $result = flat_map(
            $input,
            fn (int $value, int $index): array => $value % 2 === 0 ? [$value, $value] : [],
        );

        $this->assertSame([2, 2, 4, 4], $result);
    }

    public function testFlatMapOnListInputRenumbersKeys(): void
    {
        $input = ['a', 'b'];

        $result = flat_map(
            $input,
            fn (string $value, int $index): array => [
                'upper' => strtoupper($value),
                'lower' => $value,
            ],
        );

        $this->assertSame(['A', 'a', 'B', 'b'], $result);
    }

    public function testFlatMapOnAssocInputUsesCallbackKeys(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = flat_map(
            $input,
            fn (int $age, string $name): array => [
                $name . '_age'   => $age,
                $name . '_adult' => $age >= 20,
            ],
        );

        $this->assertSame([
            'alice_age'   => 20,
            'alice_adult' => true,
            'bob_age'     => 17,
            'bob_adult'   => false,
        ], $result);
    }

    public function testFlatMapOnAssocModeWithDuplicatedKeysKeepsLastValueAtFirstPosition(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        // alice は a, b の順、bob は b, c の順にキーを返すので b だけが衝突する。
        $result = flat_map(
            $input,
            fn (int $age, string $name): array => $name === 'alice'
                ? ['a' => "a:{$age}", 'b' => "b:{$age}"]
                : ['b' => "b:{$age}", 'c' => "c:{$age}"],
            Mode::MODE_ASSOC,
        );

        // 値は後勝ちで b:17 になるが、b の位置は初出のまま a と c の間に残る。
        $this->assertSame([
            'a' => 'a:20',
            'b' => 'b:17',
            'c' => 'c:17',
        ], $result);
    }

    public function testFlatMapOnListInputWithAssocModeCollapsesCallbackListKeys(): void
    {
        $input = [1, 2];

        // callback が list を返すため内側のキーはすべて 0 になり、後勝ちで 1 件に潰れる。
        $result = flat_map(
            $input,
            fn (int $value, int $index): array => [$value],
            Mode::MODE_ASSOC,
        );

        $this->assertSame([0 => 2], $result);
    }

    public function testFlatMapFlattensOnlyOneLevel(): void
    {
        $input = [1, 2];

        $result = flat_map(
            $input,
            fn (int $value, int $index): array => [[$value], [[$value]]],
        );

        $this->assertSame([[1], [[1]], [2], [[2]]], $result);
    }

    public function testFlatMapPassesKeyToCallback(): void
    {
        $input = [10, 20];

        $result = flat_map(
            $input,
            fn (int $value, int $index): array => [$index, $value],
        );

        $this->assertSame([0, 10, 1, 20], $result);
    }

    public function testFlatMapWithExplicitListModeDropsCallbackKeys(): void
    {
        $input = [1, 2];

        $result = flat_map(
            $input,
            fn (int $value, int $index): array => ['value' => $value, 'double' => $value * 2],
            Mode::MODE_LIST,
        );

        $this->assertSame([1, 2, 2, 4], $result);
    }

    public function testFlatMapOnAssocInputWithListModeRenumbersKeys(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = flat_map(
            $input,
            fn (int $age, string $name): array => ['name' => $name, 'age' => $age],
            Mode::MODE_LIST,
        );

        $this->assertSame(['alice', 20, 'bob', 17], $result);
    }

    public function testFlatMapDoesNotMutateInput(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        flat_map(
            $input,
            fn (int $age, string $name): array => [$name => $age * 2],
        );

        $this->assertSame([
            'alice' => 20,
            'bob'   => 17,
        ], $input);
    }
}
