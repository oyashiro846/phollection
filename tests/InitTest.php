<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class InitTest extends TestCase
{
    public function testInitListWithAutoMode(): void
    {
        $input  = [1, 2, 3];
        $result = init($input);

        $this->assertSame([1, 2], $result);
    }

    public function testInitAssocWithAutoMode(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = init($input);

        $this->assertSame(['a' => 1, 'b' => 2], $result);
    }

    public function testInitListWithListMode(): void
    {
        $input  = [1, 2, 3];
        $result = init($input, Mode::MODE_LIST);

        $this->assertSame([1, 2], $result);
    }

    public function testInitAssocWithAssocMode(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = init($input, Mode::MODE_ASSOC);

        $this->assertSame(['a' => 1, 'b' => 2], $result);
    }

    public function testInitSingleElementList(): void
    {
        $input  = [42];
        $result = init($input);

        $this->assertSame([], $result);
    }

    public function testInitSingleElementAssoc(): void
    {
        $input  = ['a' => 42];
        $result = init($input);

        $this->assertSame([], $result);
    }

    public function testInitNumericAssocWithAssocMode(): void
    {
        $input  = [10 => 1, 20 => 2, 30 => 3];
        $result = init($input, Mode::MODE_ASSOC);

        $this->assertSame([10 => 1, 20 => 2], $result);
    }

    public function testInitNonSequentialNumericKeysWithAutoMode(): void
    {
        $input  = [2 => 'a', 0 => 'b', 1 => 'c'];
        $result = init($input);

        // キーが 0 始まりの連番でないため ASSOC 扱いとなり、キーが保持される
        $this->assertSame([2 => 'a', 0 => 'b'], $result);
    }

    public function testInitEmptyList(): void
    {
        $input  = [];
        $result = init($input);

        $this->assertSame([], $result);
    }
}
