<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class CountByTest extends TestCase
{
    public function testCountByOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = count_by(
            $input,
            fn ($value, $index): string => 'all',
        );

        $this->assertSame([], $result);
    }

    public function testCountByWithSingleKey(): void
    {
        $input = [1, 2, 3];

        $result = count_by(
            $input,
            fn (int $value, int $index): string => 'all',
        );

        $this->assertSame(['all' => 3], $result);
    }

    public function testCountByWithMultipleKeysMixed(): void
    {
        $input = [3, 1, 4, 2, 5, 6];

        $result = count_by(
            $input,
            fn (int $value, int $index): string => $value % 2 === 0 ? 'even' : 'odd',
        );

        $this->assertSame([
            'odd'  => 3,
            'even' => 3,
        ], $result);
    }

    public function testCountByKeepsFirstAppearanceOrder(): void
    {
        $input = ['b', 'a', 'b', 'c', 'a'];

        $result = count_by(
            $input,
            fn (string $value): string => $value,
        );

        $this->assertSame([
            'b' => 2,
            'a' => 2,
            'c' => 1,
        ], $result);
        $this->assertSame(['b', 'a', 'c'], array_keys($result));
    }

    public function testCountByWithIntegerClassificationKeys(): void
    {
        $input = [10, 25, 30, 42];

        $result = count_by(
            $input,
            fn (int $v): int => intdiv($v, 10),
        );

        $this->assertSame([
            1 => 1,
            2 => 1,
            3 => 1,
            4 => 1,
        ], $result);
    }

    public function testCountByOnAssocInputReceivesKeys(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
            'dave'  => 18,
        ];

        $result = count_by(
            $input,
            fn (int $age, string $name): string => $name[0],
        );

        $this->assertSame([
            'a' => 1,
            'b' => 1,
            'c' => 1,
            'd' => 1,
        ], $result);
    }

    public function testCountByDoesNotMutateInput(): void
    {
        $input = [1, 2, 3];

        count_by(
            $input,
            fn (int $value): string => $value % 2 === 0 ? 'even' : 'odd',
        );

        $this->assertSame([1, 2, 3], $input);
    }

    public function testCountByResultValuesArePositiveInt(): void
    {
        $input = [1, 2, 3, 4];

        $result = count_by(
            $input,
            fn (int $value): string => $value % 2 === 0 ? 'even' : 'odd',
        );

        $this->requiresPositiveIntValues($result);

        $this->assertSame(['odd' => 2, 'even' => 2], $result);
    }

    /**
     * @param array<array-key, positive-int> $counts
     */
    private function requiresPositiveIntValues(array $counts): void
    {
        foreach ($counts as $count) {
            $this->assertGreaterThan(0, $count);
        }
    }
}
