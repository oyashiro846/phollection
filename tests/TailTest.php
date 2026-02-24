<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class TailTest extends TestCase
{
    public function testTailListWithAutoMode(): void
    {
        $input  = [1, 2, 3];
        $result = tail($input);

        $this->assertSame([2, 3], $result);
    }

    public function testTailAssocWithAutoMode(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = tail($input);

        $this->assertSame(['b' => 2, 'c' => 3], $result);
    }

    public function testTailListWithListMode(): void
    {
        $input  = [1, 2, 3];
        $result = tail($input, Mode::MODE_LIST);

        $this->assertSame([2, 3], $result);
    }

    public function testTailAssocWithAssocMode(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = tail($input, Mode::MODE_ASSOC);

        $this->assertSame(['b' => 2, 'c' => 3], $result);
    }

    public function testTailSingleElementList(): void
    {
        $input  = [42];
        $result = tail($input);

        $this->assertSame([], $result);
    }

    public function testTailSingleElementAssoc(): void
    {
        $input  = ['a' => 42];
        $result = tail($input);

        $this->assertSame([], $result);
    }

    public function testTailNumericAssocWithAssocMode(): void
    {
        $input  = [10 => 1, 20 => 2, 30 => 3];
        $result = tail($input, Mode::MODE_ASSOC);

        $this->assertSame([20 => 2, 30 => 3], $result);
    }
}
