<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class TakeWhileTest extends TestCase
{
    public function testTakeWhileOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = take_while(
            $input,
            fn ($value, $key): bool => true,
        );

        $this->assertSame([], $result);
    }

    public function testTakeWhileWhenAllElementsSatisfyPredicateReturnsAll(): void
    {
        /** @var list<int> $input */
        $input = [1, 2, 3, 4];

        $result = take_while(
            $input,
            fn (int $v): bool => $v < 10,
        );

        $this->assertSame([1, 2, 3, 4], $result);
    }

    public function testTakeWhileWhenFirstElementFailsReturnsEmpty(): void
    {
        $input = [5, 1, 2, 3];

        $result = take_while(
            $input,
            fn (int $v): bool => $v < 3,
        );

        $this->assertSame([], $result);
    }

    public function testTakeWhileStopsAtFirstFailingElement(): void
    {
        $input = [1, 2, 5, 6, 1];

        $result = take_while(
            $input,
            fn (int $v): bool => $v < 5,
        );

        $this->assertSame([1, 2], $result);
    }

    public function testTakeWhileDiffersFromFilterWhenConditionIsSatisfiedAgainAfterBoundary(): void
    {
        $input     = [1, 2, 5, 1, 2];
        $predicate = fn (int $v): bool => $v < 3;

        $takeWhileResult = take_while($input, $predicate);
        $filterResult    = filter($input, $predicate);

        $this->assertSame([1, 2], $takeWhileResult);
        $this->assertSame([1, 2, 1, 2], $filterResult);
        $this->assertNotSame($filterResult, $takeWhileResult);
    }

    public function testTakeWhileOnAssocInputPreservesKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 5,
            'd' => 1,
        ];

        $result = take_while(
            $input,
            fn (int $v): bool => $v < 3,
        );

        $this->assertSame(['a' => 1, 'b' => 2], $result);
    }

    public function testTakeWhileOnAssocInputWithListModeReindexes(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 5,
        ];

        $result = take_while(
            $input,
            fn (int $v): bool => $v < 3,
            Mode::MODE_LIST,
        );

        $this->assertSame([1, 2], $result);
    }

    public function testTakeWhilePassesCorrectKeyToPredicate(): void
    {
        $input = [
            'x' => 10,
            'y' => 20,
            'z' => 30,
        ];
        $seenKeys = [];

        take_while(
            $input,
            function (int $value, string $key) use (&$seenKeys): bool {
                $seenKeys[] = $key;

                return true;
            },
        );

        $this->assertSame(['x', 'y', 'z'], $seenKeys);
    }

    public function testTakeWhileDoesNotMutateInputArray(): void
    {
        $input    = [1, 2, 3];
        $original = $input;

        take_while(
            $input,
            fn (int $v): bool => $v < 2,
        );

        $this->assertSame($original, $input);
    }

    public function testTakeWhileStopsCallingPredicateAfterBoundaryIsDetermined(): void
    {
        $input     = [10, 20, 3, 40, 50];
        $callCount = 0;

        $result = take_while(
            $input,
            function (int $v) use (&$callCount): bool {
                ++$callCount;

                return $v > 5;
            },
        );

        $this->assertSame(3, $callCount);
        $this->assertSame([10, 20], $result);
    }
}
