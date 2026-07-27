<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class PartitionTest extends TestCase
{
    public function testPartitionOnEmptyArrayReturnsTwoEmptyArrays(): void
    {
        $input = [];

        $result = partition(
            $input,
            fn (mixed $value, int|string $key): bool => true,
        );

        $this->assertSame([[], []], $result);
    }

    public function testPartitionWhenAllElementsMatchReturnsEmptyFalseSide(): void
    {
        /** @var list<int> $input 定数畳み込みで述語が常に true と判定されるのを避ける */
        $input = [1, 2, 3];

        $result = partition(
            $input,
            fn (int $value): bool => $value > 0,
        );

        $this->assertSame([[1, 2, 3], []], $result);
    }

    public function testPartitionWhenNoElementMatchesReturnsEmptyTrueSide(): void
    {
        /** @var list<int> $input 定数畳み込みで述語が常に false と判定されるのを避ける */
        $input = [1, 2, 3];

        $result = partition(
            $input,
            fn (int $value): bool => $value > 10,
        );

        $this->assertSame([[], [1, 2, 3]], $result);
    }

    public function testPartitionOnListInputKeepsOrderInBothSides(): void
    {
        $input = [3, 1, 4, 2, 5, 6];

        $result = partition(
            $input,
            fn (int $value): bool => $value % 2 === 0,
        );

        $this->assertSame([[4, 2, 6], [3, 1, 5]], $result);
    }

    public function testPartitionOnAssocInputPreservesKeysInBothSides(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = partition(
            $input,
            fn (int $age, string $name): bool => $age >= 20,
        );

        $this->assertSame([
            [
                'alice' => 20,
                'carol' => 23,
            ],
            [
                'bob'  => 17,
                'dave' => 18,
            ],
        ], $result);
    }

    public function testPartitionOnAssocInputWithListModeRenumbersBothSides(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = partition(
            $input,
            fn (int $age): bool => $age >= 20,
            Mode::MODE_LIST,
        );

        $this->assertSame([[20, 23], [17, 18]], $result);
    }

    public function testPartitionOnListInputWithAssocModePreservesIndexes(): void
    {
        $input = [3, 1, 4, 2];

        $result = partition(
            $input,
            fn (int $value): bool => $value % 2 === 0,
            Mode::MODE_ASSOC,
        );

        $this->assertSame([
            [2 => 4, 3 => 2],
            [0 => 3, 1 => 1],
        ], $result);
    }

    public function testPartitionPassesKeyToCallback(): void
    {
        $input = [10, 20, 30, 40];

        $result = partition(
            $input,
            fn (int $value, int $index): bool => $index % 2 === 0,
        );

        $this->assertSame([[10, 30], [20, 40]], $result);
    }

    public function testPartitionKeepsTotalCountOfInput(): void
    {
        $input = [
            'a' => 5,
            'b' => 8,
            'c' => 13,
            'd' => 21,
            'e' => 34,
            'f' => 55,
        ];

        [$matched, $unmatched] = partition(
            $input,
            fn (int $value): bool => $value % 2 === 0,
        );

        $this->assertSame(\count($input), \count($matched) + \count($unmatched));
    }

    public function testPartitionDoesNotModifyInput(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        partition(
            $input,
            fn (int $age): bool => $age >= 20,
        );

        $this->assertSame([
            'alice' => 20,
            'bob'   => 17,
        ], $input);
    }
}
