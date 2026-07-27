<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class DropTest extends TestCase
{
    public function testDropOnEmptyArrayReturnsEmpty(): void
    {
        $result = drop([], 2);

        $this->assertSame([], $result);
    }

    public function testDropWithZeroNReturnsAll(): void
    {
        $result = drop([1, 2, 3], 0);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testDropWithNegativeNReturnsAll(): void
    {
        $result = drop([1, 2, 3], -1);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testDropWithNLessThanCountReturnsPartial(): void
    {
        $result = drop([1, 2, 3, 4], 2);

        $this->assertSame([3, 4], $result);
    }

    public function testDropWithNEqualToCountReturnsEmpty(): void
    {
        $result = drop([1, 2, 3], 3);

        $this->assertSame([], $result);
    }

    public function testDropWithNGreaterThanCountReturnsEmpty(): void
    {
        $result = drop([1, 2, 3], 10);

        $this->assertSame([], $result);
    }

    public function testDropOnAssocInputPreservesKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $result = drop($input, 1);

        $this->assertSame([
            'b' => 2,
            'c' => 3,
        ], $result);
    }

    public function testDropOnAssocInputWithModeListReindexesRemainingKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $result = drop($input, 1, Mode::MODE_LIST);

        $this->assertSame([2, 3], $result);
    }

    public function testDropOnListInputWithModeAssocKeepsOriginalIntegerKeys(): void
    {
        $result = drop([1, 2, 3], 1, Mode::MODE_ASSOC);

        $this->assertSame([
            1 => 2,
            2 => 3,
        ], $result);
    }

    public function testDropDoesNotMutateInput(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];

        drop($input, 2);

        $this->assertSame([
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ], $input);
    }
}
