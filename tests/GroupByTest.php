<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class GroupByTest extends TestCase
{
    public function testGroupByOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = group_by(
            $input,
            fn ($value, $index): string => 'even',
        );

        $this->assertSame([], $result);
    }

    public function testGroupByWithSingleGroupOnListInput(): void
    {
        $input = [1, 2, 3];

        $result = group_by(
            $input,
            fn (int $value, int $index): string => 'all',
        );

        $this->assertSame([
            'all' => [1, 2, 3],
        ], $result);
    }

    public function testGroupByWithMultipleGroupsOnListInputKeepsOrder(): void
    {
        $input = [3, 1, 4, 2, 5, 6];

        $result = group_by(
            $input,
            fn (int $value, int $index): string => $value % 2 === 0 ? 'even' : 'odd',
        );

        $this->assertSame([
            'odd'  => [3, 1, 5],
            'even' => [4, 2, 6],
        ], $result);
    }

    public function testGroupByOnAssocInputPreservesKeysInEachGroup(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = group_by(
            $input,
            fn (int $age, string $name): string => $age >= 20 ? 'adult' : 'minor',
        );

        $this->assertSame([
            'adult' => [
                'alice' => 20,
                'carol' => 23,
            ],
            'minor' => [
                'bob'  => 17,
                'dave' => 18,
            ],
        ], $result);
    }
}
