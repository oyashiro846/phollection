<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class SliceTest extends TestCase
{
    public function testSliceListWithAutoMode(): void
    {
        $result = slice([10, 20, 30, 40], 1, 2);

        $this->assertSame([20, 30], $result);
    }

    public function testSliceListWithListMode(): void
    {
        $result = slice([1, 2, 3, 4], 2, null, Mode::MODE_LIST);
        $this->assertSame([3, 4], $result);
    }

    public function testSliceAssocWithAutoMode(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'd' => 4,
        ];
        $result = slice($input, 1, 2);
        $this->assertSame([
            'b' => 2,
            'c' => 3,
        ], $result);
    }

    public function testSliceAssocWithAssocMode(): void
    {
        $input = [
            'x' => 10,
            'y' => 20,
            'z' => 30,
        ];
        $result = slice($input, 1, null, Mode::MODE_ASSOC);
        $this->assertSame([
            'y' => 20,
            'z' => 30,
        ], $result);
    }

    public function testSliceWithNegativeOffset(): void
    {
        $result = slice([1, 2, 3, 4], -2);
        $this->assertSame([3, 4], $result);
    }

    public function testSliceWithTooLongLength(): void
    {
        $result = slice([1, 2, 3, 4], 2, 10);
        $this->assertSame([3, 4], $result);
    }

    public function testSliceOnEmptyArray(): void
    {
        $result = slice([], 0, 10);
        $this->assertSame([], $result);
    }
}
