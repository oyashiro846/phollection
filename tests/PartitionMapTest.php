<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class PartitionMapTest extends TestCase
{
    public function testPartitionMapOnEmptyArrayReturnsTwoEmptyArrays(): void
    {
        $input = [];

        $result = partition_map(
            $input,
            fn (mixed $value, int|string $key): array => [true, $value],
        );

        $this->assertSame([[], []], $result);
    }

    public function testPartitionMapWhenAllElementsGoLeftReturnsEmptyRightSide(): void
    {
        $input = [1, 2, 3];

        $result = partition_map(
            $input,
            fn (int $value): array => [true, $value * 2],
        );

        $this->assertSame([[2, 4, 6], []], $result);
    }

    public function testPartitionMapWhenAllElementsGoRightReturnsEmptyLeftSide(): void
    {
        $input = [1, 2, 3];

        $result = partition_map(
            $input,
            fn (int $value): array => [false, "v{$value}"],
        );

        $this->assertSame([[], ['v1', 'v2', 'v3']], $result);
    }

    public function testPartitionMapWithMixedResultsKeepsDifferentValueTypesAndOrder(): void
    {
        $input = [3, 1, 4, 2, 5, 6];

        $result = partition_map(
            $input,
            fn (int $value): array => $value % 2 === 0
                ? [true, $value * 10]
                : [false, "odd:{$value}"],
        );

        $this->assertSame([
            [40, 20, 60],
            ['odd:3', 'odd:1', 'odd:5'],
        ], $result);
    }

    public function testPartitionMapOnAssocInputPreservesKeysInBothSides(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = partition_map(
            $input,
            fn (int $age): array => $age >= 20
                ? [true, $age]
                : [false, "minor({$age})"],
        );

        $this->assertSame([
            [
                'alice' => 20,
                'carol' => 23,
            ],
            [
                'bob'  => 'minor(17)',
                'dave' => 'minor(18)',
            ],
        ], $result);
    }

    public function testPartitionMapOnListInputWithAssocModePreservesIndexes(): void
    {
        $input = [3, 1, 4, 2];

        $result = partition_map(
            $input,
            fn (int $value): array => $value % 2 === 0
                ? [true, $value * 10]
                : [false, "odd:{$value}"],
            Mode::MODE_ASSOC,
        );

        $this->assertSame([
            [2 => 40, 3 => 20],
            [0 => 'odd:3', 1 => 'odd:1'],
        ], $result);
    }

    public function testPartitionMapOnAssocInputWithListModeRenumbersBothSides(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = partition_map(
            $input,
            fn (int $age): array => $age >= 20
                ? [true, $age]
                : [false, "minor({$age})"],
            Mode::MODE_LIST,
        );

        $this->assertSame([
            [20, 23],
            ['minor(17)', 'minor(18)'],
        ], $result);
    }

    public function testPartitionMapPassesKeyToCallback(): void
    {
        $input = [10, 20, 30, 40];

        $result = partition_map(
            $input,
            fn (int $value, int $index): array => $index % 2 === 0
                ? [true, $index]
                : [false, $value],
        );

        $this->assertSame([[0, 2], [20, 40]], $result);
    }

    public function testPartitionMapKeepsTotalCountOfInput(): void
    {
        $input = [
            'a' => 5,
            'b' => 8,
            'c' => 13,
            'd' => 21,
            'e' => 34,
            'f' => 55,
        ];

        [$left, $right] = partition_map(
            $input,
            fn (int $value): array => $value % 2 === 0
                ? [true, $value]
                : [false, (string) $value],
        );

        $this->assertSame(\count($input), \count($left) + \count($right));
    }

    public function testPartitionMapAcceptsCallbackDeclaredByContract(): void
    {
        $input = [3, 1, 4, 2, 6];

        // 契約どおりの戻り値型を宣言したコールバックが $callback の @param 型を
        // 通過することを PHPStan level 10 で確かめる。
        /**
         * @return array{0: true, 1: int}|array{0: false, 1: string}
         */
        $classify = static fn (int $value): array => $value % 2 === 0
            ? [true, $value * 10]
            : [false, "odd:{$value}"];

        $result = partition_map($input, $classify);

        $this->assertSame([
            [40, 20, 60],
            ['odd:3', 'odd:1'],
        ], $result);
    }

    public function testPartitionMapInfersUnionTypeOnBothSides(): void
    {
        $input = [3, 1, 4, 2, 6];

        [$left, $right] = partition_map(
            $input,
            fn (int $value): array => $value % 2 === 0
                ? [true, $value * 10]
                : [false, "odd:{$value}"],
        );

        $this->assertSame(3, $this->acceptsUnionList($left));
        $this->assertSame(2, $this->acceptsUnionList($right));
    }

    public function testPartitionMapDoesNotModifyInput(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        partition_map(
            $input,
            fn (int $age): array => $age >= 20
                ? [true, $age]
                : [false, "minor({$age})"],
        );

        $this->assertSame([
            'alice' => 20,
            'bob'   => 17,
        ], $input);
    }

    /**
     * 左右の値は単一の型パラメータ E として扱われるため、静的にはどちらの側も union に推論される。
     *
     * 判別可能 union（array{0: true, 1: L}|array{0: false, 1: R}）から L と R を別々に推論させる
     * 試みは PHPStan 2.1 では成立しなかった（両方とも union に解決される）。
     * 型パラメータを L / R の 2 本に分ける案を再検討する前に、ここで期待値を確かめること。
     *
     * @param list<int|string> $values
     */
    private function acceptsUnionList(array $values): int
    {
        return \count($values);
    }
}
