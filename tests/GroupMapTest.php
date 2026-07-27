<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class GroupMapTest extends TestCase
{
    public function testGroupMapOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = group_map(
            $input,
            fn ($value, $key): string => 'all',
            fn ($value, $key)         => $value,
        );

        $this->assertSame([], $result);
    }

    public function testGroupMapWithSingleGroupOnListInput(): void
    {
        $input = [1, 2, 3];

        $result = group_map(
            $input,
            fn (int $value, int $index): string => 'all',
            fn (int $value, int $index): int    => $value * 10,
        );

        $this->assertSame([
            'all' => [10, 20, 30],
        ], $result);
    }

    public function testGroupMapWithMultipleGroupsOnListInputKeepsOrder(): void
    {
        $input = [3, 1, 4, 2, 5, 6];

        $result = group_map(
            $input,
            fn (int $value, int $index): string => $value % 2 === 0 ? 'even' : 'odd',
            fn (int $value, int $index): int    => $value * 2,
        );

        $this->assertSame([
            'odd'  => [6, 2, 10],
            'even' => [8, 4, 12],
        ], $result);
    }

    public function testGroupMapOnAssocInputPreservesKeysInEachGroup(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = group_map(
            $input,
            fn (int $age, string $name): string => $age >= 20 ? 'adult' : 'minor',
            fn (int $age, string $name): int    => $age + 1,
        );

        $this->assertSame([
            'adult' => [
                'alice' => 21,
                'carol' => 24,
            ],
            'minor' => [
                'bob'  => 18,
                'dave' => 19,
            ],
        ], $result);
    }

    public function testGroupMapOnAssocInputWithListModeDropsKeysInEachGroup(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = group_map(
            $input,
            fn (int $age, string $name): string => $age >= 20 ? 'adult' : 'minor',
            fn (int $age, string $name): int    => $age + 1,
            Mode::MODE_LIST,
        );

        $this->assertSame([
            'adult' => [21, 24],
            'minor' => [18, 19],
        ], $result);

        $this->assertEveryGroupIsList($result);
    }

    public function testGroupMapOnListInputWithAssocModeKeepsOriginalIndexes(): void
    {
        $input = [3, 1, 4, 2, 5, 6];

        $result = group_map(
            $input,
            fn (int $value, int $index): string => $value % 2 === 0 ? 'even' : 'odd',
            fn (int $value, int $index): int    => $value * 2,
            Mode::MODE_ASSOC,
        );

        // 元の添字を維持するため、グループ内の添字は飛び飛びのままになる。
        $this->assertSame([
            'odd' => [
                0 => 6,
                1 => 2,
                4 => 10,
            ],
            'even' => [
                2 => 8,
                3 => 4,
                5 => 12,
            ],
        ], $result);
    }

    public function testGroupMapWithIntegerGroupKeys(): void
    {
        $input = [10, 25, 30, 42];

        $result = group_map(
            $input,
            fn (int $value, int $index): int => intdiv($value, 10),
            fn (int $value, int $index): int => $value % 10,
        );

        $this->assertSame([
            1 => [0],
            2 => [5],
            3 => [0],
            4 => [2],
        ], $result);
    }

    public function testGroupMapTransformChangesValueType(): void
    {
        $input = [1, 2, 3];

        $result = group_map(
            $input,
            fn (int $value, int $index): string => $value % 2 === 0 ? 'even' : 'odd',
            fn (int $value, int $index): string => "#{$value}",
        );

        $this->assertSame([
            'odd'  => ['#1', '#3'],
            'even' => ['#2'],
        ], $result);
    }

    public function testGroupMapPassesKeyToBothCallbacks(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = group_map(
            $input,
            fn (int $age, string $name): string => $name[0],
            fn (int $age, string $name): string => "{$name}:{$age}",
        );

        $this->assertSame([
            'a' => ['alice' => 'alice:20'],
            'b' => ['bob' => 'bob:17'],
            'c' => ['carol' => 'carol:23'],
        ], $result);
    }

    public function testGroupMapDoesNotMutateInput(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        group_map(
            $input,
            fn (int $age, string $name): string => 'all',
            fn (int $age, string $name): int    => $age * 2,
        );

        $this->assertSame([
            'alice' => 20,
            'bob'   => 17,
        ], $input);
    }

    /**
     * 各グループが実行時に list であることを確かめます。
     *
     * 条件型を持たない @return と同じ緩い型で受け取るのは、group_map() の呼び出し地点のまま
     * array_is_list() を呼ぶと @phpstan-return の list<E> から常に真だと分かってしまい、
     * 検査として成立しないためです。
     *
     * @param array<string, list<int>|array<string, int>> $groups
     */
    private function assertEveryGroupIsList(array $groups): void
    {
        foreach ($groups as $group) {
            $this->assertTrue(array_is_list($group));
        }
    }
}
