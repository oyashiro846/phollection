<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class MergeTest extends TestCase
{
    public function testMergeWithEmptyWithAutoMode(): void
    {
        $result = merge([[], [], []]);

        $this->assertSame([], $result);
    }

    public function testMergeWithEmptyWithListMode(): void
    {
        $result = merge([[], [], []], Mode::MODE_LIST);

        $this->assertSame([], $result);
    }

    public function testMergeWithEmptyWithAssocMode(): void
    {
        $result = merge([[], [], []], Mode::MODE_ASSOC);

        $this->assertSame([], $result);
    }

    public function testMergeWithListInputWithAutoMode(): void
    {
        $input1 = [1, 2, 3];
        $input2 = [3, 4, 5];

        $result = merge([$input1, $input2]);

        $this->assertSame([1, 2, 3, 3, 4, 5], $result);
    }

    public function testMergeWithStringKeyAssocInputWithAutoMode(): void
    {
        $input1 = ['a' => 1, 'b' => 2];
        $input2 = ['b' => 3, 'c' => 4];

        $result = merge([$input1, $input2]);

        $this->assertSame(['a' => 1, 'b' => 3, 'c' => 4], $result);
    }

    public function testMergeWithNumberKeyAssocInputWithListMode(): void
    {
        $input1 = [1 => 'a', 2 => 'b', 4 => 'd'];
        $input2 = [5 => 'e', 7 => 'g', 9 => 'i'];

        $result = merge([$input1, $input2], Mode::MODE_LIST);

        $this->assertSame([0 => 'a', 1 => 'b', 2 => 'd', 3 => 'e', 4 => 'g', 5 => 'i'], $result);
    }

    public function testMergeWithNumberKeyAssocInputWithAssocMode(): void
    {
        $input1 = [1 => 'a', 2 => 'b', 4 => 'd'];
        $input2 = [5 => 'e', 7 => 'g', 9 => 'i'];

        $result = merge([$input1, $input2], Mode::MODE_ASSOC);

        $this->assertSame([1 => 'a', 2 => 'b', 4 => 'd', 5 => 'e', 7 => 'g', 9 => 'i'], $result);
    }

    public function testMergeWithMixKeyAssocInputWithAutoMode(): void
    {
        $input1 = [1, 2, 3, 4, 5];
        $input2 = [3 => '4', 4 => '5'];
        $input3 = ['f' => '6', 'g' => '7'];

        $result = merge([$input1, $input2, $input3]);

        $this->assertSame([1, 2, 3, 4, 5, '4', '5', 'f' => '6', 'g' => '7'], $result);
    }

    public function testMergeWithMixKeyAssocInputWithListMode(): void
    {
        $input1 = [1, 2, 3, 4, 5];
        $input2 = [3 => '4', 4 => '5'];
        $input3 = ['f' => '6', 'g' => '7'];

        $result = merge([$input1, $input2, $input3], Mode::MODE_LIST);

        $this->assertSame([1, 2, 3, 4, 5, '4', '5', '6', '7'], $result);
    }

    public function testMergeWithMixKeyAssocInputWithAssocMode(): void
    {
        $input1 = [1, 2, 3, 4, 5];
        $input2 = [3 => '4', 4 => '5'];
        $input3 = ['f' => '6', 'g' => '7'];

        $result = merge([$input1, $input2, $input3], Mode::MODE_ASSOC);

        $this->assertSame([1, 2, 3, '4', '5', 'f' => '6', 'g' => '7'], $result);
    }
}
