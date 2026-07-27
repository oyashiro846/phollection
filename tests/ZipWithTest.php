<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class ZipWithTest extends TestCase
{
    public function testZipWithOnBothEmptyReturnsEmpty(): void
    {
        $result = zip_with([], [], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([], $result);
    }

    public function testZipWithOnLeftEmptyReturnsEmpty(): void
    {
        $result = zip_with([], [1, 2, 3], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([], $result);
    }

    public function testZipWithOnRightEmptyReturnsEmpty(): void
    {
        $result = zip_with([1, 2, 3], [], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([], $result);
    }

    public function testZipWithOnSameLengthAddsNumbers(): void
    {
        $result = zip_with([1, 2, 3], [10, 20, 30], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([11, 22, 33], $result);
    }

    public function testZipWithTruncatesToLeftWhenLeftIsShorter(): void
    {
        $result = zip_with([1, 2], [10, 20, 30], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([11, 22], $result);
    }

    public function testZipWithTruncatesToRightWhenRightIsShorter(): void
    {
        $result = zip_with([1, 2, 3], [10, 20], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([11, 22], $result);
    }

    public function testZipWithOnAssocInputsWithModeListIgnoresKeysAndPairsByIterationOrder(): void
    {
        $left = [
            'x' => 1,
            'y' => 2,
        ];
        $right = [
            'b' => 10,
            'a' => 20,
        ];

        $result = zip_with($left, $right, fn (int $l, int $r): int => $l + $r, Mode::MODE_LIST);

        $this->assertSame([11, 22], $result);
    }

    public function testZipWithOnAssocInputsWithModeAssocPreservesLeftKeys(): void
    {
        $left = [
            'a' => 1,
            'b' => 2,
        ];
        $right = [
            'x' => 10,
            'y' => 20,
        ];

        $result = zip_with($left, $right, fn (int $l, int $r): int => $l + $r, Mode::MODE_ASSOC);

        $this->assertSame([
            'a' => 11,
            'b' => 22,
        ], $result);
    }

    public function testZipWithOnAssocLeftByDefaultPreservesLeftKeys(): void
    {
        $result = zip_with(['a' => 1, 'b' => 2], [10, 20], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([
            'a' => 11,
            'b' => 22,
        ], $result);
    }

    public function testZipWithOnListInputsWithModeAssocKeepsSequentialKeys(): void
    {
        $result = zip_with([1, 2], [10, 20], fn (int $l, int $r): int => $l + $r, Mode::MODE_ASSOC);

        $this->assertSame([11, 22], $result);
    }

    public function testZipWithOnListLeftAndAssocRightReturnsList(): void
    {
        $result = zip_with([1, 2], ['x' => 10, 'y' => 20], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([11, 22], $result);
    }

    public function testZipWithOnAssocLeftAndListRightPreservesLeftKeys(): void
    {
        $result = zip_with(['a' => 1, 'b' => 2], [10, 20], fn (int $l, int $r): int => $l + $r);

        $this->assertSame([
            'a' => 11,
            'b' => 22,
        ], $result);
    }

    public function testZipWithModeAssocPreservesLeadingLeftKeysWhenRightIsShorter(): void
    {
        $left = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $result = zip_with($left, [10, 20], fn (int $l, int $r): int => $l + $r, Mode::MODE_ASSOC);

        $this->assertSame([
            'a' => 11,
            'b' => 22,
        ], $result);
    }

    public function testZipWithModeAssocPreservesAllLeftKeysWhenLeftIsShorter(): void
    {
        $left = [
            'a' => 1,
            'b' => 2,
        ];

        $result = zip_with($left, [10, 20, 30], fn (int $l, int $r): int => $l + $r, Mode::MODE_ASSOC);

        $this->assertSame([
            'a' => 11,
            'b' => 22,
        ], $result);
    }

    public function testZipWithOnNumericKeyedLeftWithModeListRenumbersKeys(): void
    {
        $left = [
            3 => 1,
            7 => 2,
        ];

        $result = zip_with($left, [10, 20], fn (int $l, int $r): int => $l + $r, Mode::MODE_LIST);

        $this->assertSame([11, 22], $result);
    }

    public function testZipWithCallbackReturningArrayIsNotFlattened(): void
    {
        $result = zip_with(
            [1, 2],
            ['a', 'b'],
            fn (int $l, string $r): array => [$l, $r],
        );

        $this->assertSame([
            [1, 'a'],
            [2, 'b'],
        ], $result);
    }

    public function testZipWithDoesNotMutateInputArrays(): void
    {
        $left  = [1, 2, 3];
        $right = [10, 20];

        zip_with($left, $right, fn (int $l, int $r): int => $l + $r);

        $this->assertSame([1, 2, 3], $left);
        $this->assertSame([10, 20], $right);
    }
}
