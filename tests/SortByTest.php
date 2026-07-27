<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class SortByTest extends TestCase
{
    public function testSortByOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = sort_by(
            $input,
            fn ($value): mixed => $value,
        );

        $this->assertSame([], $result);
    }

    public function testSortByOnSingleElementReturnsSameElement(): void
    {
        $input = [42];

        $result = sort_by(
            $input,
            fn (int $value): int => $value,
        );

        $this->assertSame([42], $result);
    }

    public function testSortByWithNumericKeyAscendsInOrder(): void
    {
        $input = [3, 1, 4, 1, 5, 9, 2, 6];

        $result = sort_by(
            $input,
            fn (int $value): int => $value,
        );

        $this->assertSame([1, 1, 2, 3, 4, 5, 6, 9], $result);
    }

    public function testSortByWithStringKeyAscendsInOrder(): void
    {
        $input = ['banana', 'apple', 'cherry'];

        $result = sort_by(
            $input,
            fn (string $value): string => $value,
        );

        $this->assertSame(['apple', 'banana', 'cherry'], $result);
    }

    public function testSortByWithEqualSortKeysKeepsInputOrder(): void
    {
        $input = [
            ['name' => 'alice', 'priority' => 1],
            ['name' => 'bob', 'priority' => 1],
            ['name' => 'carol', 'priority' => 0],
            ['name' => 'dave', 'priority' => 1],
        ];

        $result = sort_by(
            $input,
            fn (array $item): int => $item['priority'],
        );

        $this->assertSame([
            ['name' => 'carol', 'priority' => 0],
            ['name' => 'alice', 'priority' => 1],
            ['name' => 'bob', 'priority' => 1],
            ['name' => 'dave', 'priority' => 1],
        ], $result);
    }

    public function testSortByOnAssocInputPreservesKeys(): void
    {
        $input = [
            'carol' => 23,
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = sort_by(
            $input,
            fn (int $age): int => $age,
        );

        $this->assertSame([
            'bob'   => 17,
            'alice' => 20,
            'carol' => 23,
        ], $result);
    }

    public function testSortByOnAssocInputWithModeListReindexesFromZero(): void
    {
        $input = [
            'carol' => 23,
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = sort_by(
            $input,
            fn (int $age): int => $age,
            Mode::MODE_LIST,
        );

        $this->assertSame([17, 20, 23], $result);
    }

    public function testSortBySelectorReceivesKeyAsSecondArgument(): void
    {
        $input = [
            'carol' => 3,
            'alice' => 1,
            'bob'   => 2,
        ];

        $result = sort_by(
            $input,
            fn (int $value, string $key): string => $key,
        );

        $this->assertSame([
            'alice' => 1,
            'bob'   => 2,
            'carol' => 3,
        ], $result);
    }

    public function testSortByWithExplicitModeAssocKeepsOriginalKeys(): void
    {
        $input = [3, 1, 2];

        $result = sort_by(
            $input,
            fn (int $value): int => $value,
            Mode::MODE_ASSOC,
        );

        $this->assertSame([1 => 1, 2 => 2, 0 => 3], $result);
    }

    public function testSortByDoesNotMutateInputArray(): void
    {
        $input = [3, 1, 2];

        sort_by(
            $input,
            fn (int $value): int => $value,
        );

        $this->assertSame([3, 1, 2], $input);
    }
}
