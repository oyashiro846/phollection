<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class TakeTest extends TestCase
{
    public function testTakeOnEmptyArrayReturnsEmpty(): void
    {
        $result = take([], 2);

        $this->assertSame([], $result);
    }

    public function testTakeWithZeroNReturnsEmpty(): void
    {
        $result = take([1, 2, 3], 0);

        $this->assertSame([], $result);
    }

    public function testTakeWithNegativeNReturnsEmpty(): void
    {
        $result = take([1, 2, 3], -1);

        $this->assertSame([], $result);
    }

    public function testTakeWithNLessThanCountReturnsPartial(): void
    {
        $result = take([1, 2, 3, 4], 2);

        $this->assertSame([1, 2], $result);
    }

    public function testTakeWithNEqualToCountReturnsAll(): void
    {
        $result = take([1, 2, 3], 3);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testTakeWithNGreaterThanCountReturnsAll(): void
    {
        $result = take([1, 2, 3], 10);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testTakeOnAssocInputPreservesKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $result = take($input, 2);

        $this->assertSame([
            'a' => 1,
            'b' => 2,
        ], $result);
    }

    public function testTakeOnAssocInputWithModeListReindexesKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $result = take($input, 2, Mode::MODE_LIST);

        $this->assertSame([1, 2], $result);
    }

    public function testTakeOnListInputWithModeAssocPreservesOriginalKeys(): void
    {
        $result = take([1, 2, 3], 2, Mode::MODE_ASSOC);

        $this->assertSame([
            0 => 1,
            1 => 2,
        ], $result);
    }

    public function testTakeDoesNotMutateInput(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];

        take($input, 2);

        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ], $input);
    }
}
