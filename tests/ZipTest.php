<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class ZipTest extends TestCase
{
    public function testZipOnBothEmptyReturnsEmpty(): void
    {
        $result = zip([], []);

        $this->assertSame([], $result);
    }

    public function testZipOnLeftEmptyReturnsEmpty(): void
    {
        $result = zip([], [1, 2, 3]);

        $this->assertSame([], $result);
    }

    public function testZipOnRightEmptyReturnsEmpty(): void
    {
        $result = zip([1, 2, 3], []);

        $this->assertSame([], $result);
    }

    public function testZipOnSameLengthListsPairsInOrder(): void
    {
        $result = zip([1, 2, 3], ['a', 'b', 'c']);

        $this->assertSame([
            [1, 'a'],
            [2, 'b'],
            [3, 'c'],
        ], $result);
    }

    public function testZipTruncatesToLeftWhenLeftIsShorter(): void
    {
        $result = zip([1, 2], ['a', 'b', 'c']);

        $this->assertSame([
            [1, 'a'],
            [2, 'b'],
        ], $result);
    }

    public function testZipTruncatesToRightWhenRightIsShorter(): void
    {
        $result = zip([1, 2, 3], ['a', 'b']);

        $this->assertSame([
            [1, 'a'],
            [2, 'b'],
        ], $result);
    }

    public function testZipOnAssocInputsWithModeListIgnoresKeysAndPairsByIterationOrder(): void
    {
        $left = [
            'x' => 1,
            'y' => 2,
        ];
        $right = [
            'b' => 'first',
            'a' => 'second',
        ];

        $result = zip($left, $right, Mode::MODE_LIST);

        $this->assertSame([
            [1, 'first'],
            [2, 'second'],
        ], $result);
    }

    public function testZipOnAssocInputsWithModeAssocPreservesLeftKeys(): void
    {
        $left = [
            'a' => 1,
            'b' => 2,
        ];
        $right = [
            'x' => 10,
            'y' => 20,
        ];

        $result = zip($left, $right, Mode::MODE_ASSOC);

        $this->assertSame([
            'a' => [1, 10],
            'b' => [2, 20],
        ], $result);
    }

    public function testZipOnAssocLeftByDefaultPreservesLeftKeys(): void
    {
        $result = zip(['a' => 1, 'b' => 2], [10, 20]);

        $this->assertSame([
            'a' => [1, 10],
            'b' => [2, 20],
        ], $result);
    }

    public function testZipOnListInputsWithModeAssocKeepsSequentialKeys(): void
    {
        $result = zip([1, 2], [10, 20], Mode::MODE_ASSOC);

        $this->assertSame([
            [1, 10],
            [2, 20],
        ], $result);
    }

    public function testZipOnListLeftAndAssocRightReturnsList(): void
    {
        $result = zip([1, 2], ['x' => 10, 'y' => 20]);

        $this->assertSame([
            [1, 10],
            [2, 20],
        ], $result);
    }

    public function testZipOnAssocLeftAndListRightPreservesLeftKeys(): void
    {
        $result = zip(['a' => 1, 'b' => 2], [10, 20]);

        $this->assertSame([
            'a' => [1, 10],
            'b' => [2, 20],
        ], $result);
    }

    public function testZipWithModeAssocPreservesLeadingLeftKeysWhenRightIsShorter(): void
    {
        $left = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $result = zip($left, [10, 20], Mode::MODE_ASSOC);

        $this->assertSame([
            'a' => [1, 10],
            'b' => [2, 20],
        ], $result);
    }

    public function testZipWithModeAssocPreservesAllLeftKeysWhenLeftIsShorter(): void
    {
        $left = [
            'a' => 1,
            'b' => 2,
        ];

        $result = zip($left, [10, 20, 30], Mode::MODE_ASSOC);

        $this->assertSame([
            'a' => [1, 10],
            'b' => [2, 20],
        ], $result);
    }

    public function testZipOnNumericKeyedLeftWithModeListRenumbersKeys(): void
    {
        $left = [
            3 => 1,
            7 => 2,
        ];

        $result = zip($left, [10, 20], Mode::MODE_LIST);

        $this->assertSame([
            [1, 10],
            [2, 20],
        ], $result);
    }

    public function testZipDoesNotMutateInputArrays(): void
    {
        $left  = [1, 2, 3];
        $right = ['a', 'b'];

        zip($left, $right);

        $this->assertSame([1, 2, 3], $left);
        $this->assertSame(['a', 'b'], $right);
    }
}
