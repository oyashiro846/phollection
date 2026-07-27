<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class DropWhileTest extends TestCase
{
    public function testDropWhileOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = drop_while(
            $input,
            fn ($value, $key): bool => true,
        );

        $this->assertSame([], $result);
    }

    public function testDropWhileWhenAllElementsSatisfyPredicateReturnsEmpty(): void
    {
        /** @var list<int> $input */
        $input = [1, 2, 3, 4];

        $result = drop_while(
            $input,
            fn (int $v): bool => $v < 10,
        );

        $this->assertSame([], $result);
    }

    public function testDropWhileWhenFirstElementFailsReturnsAll(): void
    {
        $input = [5, 1, 2, 3];

        $result = drop_while(
            $input,
            fn (int $v): bool => $v < 3,
        );

        $this->assertSame([5, 1, 2, 3], $result);
    }

    public function testDropWhileDropsUntilFirstFailingElement(): void
    {
        $input = [1, 2, 5, 6, 1];

        $result = drop_while(
            $input,
            fn (int $v): bool => $v < 5,
        );

        $this->assertSame([5, 6, 1], $result);
    }

    public function testDropWhileDiffersFromFilterWhenConditionIsSatisfiedAgainAfterBoundary(): void
    {
        $input     = [1, 2, 5, 1, 2];
        $predicate = fn (int $v): bool => $v < 3;

        $dropWhileResult = drop_while($input, $predicate);
        $filterResult    = filter($input, fn (int $v): bool => !$predicate($v));

        $this->assertSame([5, 1, 2], $dropWhileResult);
        $this->assertSame([5], $filterResult);
        $this->assertNotSame($filterResult, $dropWhileResult);
    }

    public function testDropWhileOnAssocInputPreservesKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 5,
            'd' => 1,
        ];

        $result = drop_while(
            $input,
            fn (int $v): bool => $v < 3,
        );

        $this->assertSame(['c' => 5, 'd' => 1], $result);
    }

    public function testDropWhileOnAssocInputWithListModeReindexes(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 5,
            'd' => 6,
        ];

        $result = drop_while(
            $input,
            fn (int $v): bool => $v < 3,
            Mode::MODE_LIST,
        );

        $this->assertSame([5, 6], $result);
    }

    public function testDropWhilePassesCorrectKeyToPredicate(): void
    {
        $input = [
            'x' => 10,
            'y' => 20,
            'z' => 30,
        ];
        $seenKeys = [];

        drop_while(
            $input,
            function (int $value, string $key) use (&$seenKeys): bool {
                $seenKeys[] = $key;

                return $value < 30;
            },
        );

        $this->assertSame(['x', 'y', 'z'], $seenKeys);
    }

    public function testDropWhileDoesNotMutateInputArray(): void
    {
        $input    = [1, 2, 3];
        $original = $input;

        drop_while(
            $input,
            fn (int $v): bool => $v < 2,
        );

        $this->assertSame($original, $input);
    }

    public function testDropWhileStopsCallingPredicateAfterBoundaryIsDetermined(): void
    {
        $input     = [10, 20, 3, 40, 50];
        $callCount = 0;

        $result = drop_while(
            $input,
            function (int $v) use (&$callCount): bool {
                ++$callCount;

                return $v > 5;
            },
        );

        $this->assertSame(3, $callCount);
        $this->assertSame([3, 40, 50], $result);
    }

    public function testDropWhileOnListInputWithAssocModePreservesOriginalIntegerKeys(): void
    {
        $input = [1, 2, 5, 6, 1];

        $result = drop_while(
            $input,
            fn (int $v): bool => $v < 5,
            Mode::MODE_ASSOC,
        );

        $this->assertSame([2 => 5, 3 => 6, 4 => 1], $result);
    }
}
