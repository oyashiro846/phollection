<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class HeadOptionTest extends TestCase
{
    public function testHeadOptionWithList(): void
    {
        $input  = [1, 2, 3];
        $result = Arrays::head_option($input);

        $this->assertSame(1, $result);
    }

    public function testHeadOptionWithAssoc(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = Arrays::head_option($input);

        $this->assertSame(1, $result);
    }

    public function testHeadOptionWithNumericAssoc(): void
    {
        $input  = [10 => 'a', 20 => 'b', 30 => 'c'];
        $result = Arrays::head_option($input);

        $this->assertSame('a', $result);
    }

    public function testHeadOptionWithSingleElement(): void
    {
        $input  = [42];
        $result = Arrays::head_option($input);

        $this->assertSame(42, $result);
    }

    public function testHeadOptionWithSingleElementAssoc(): void
    {
        $input  = ['key' => 'value'];
        $result = Arrays::head_option($input);

        $this->assertSame('value', $result);
    }

    public function testHeadOptionWithEmptyArray(): void
    {
        $input  = [];
        $result = Arrays::head_option($input);

        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $this->assertNull($result);
    }

    public function testHeadOptionWithNullValue(): void
    {
        $input  = [null, 1, 2];
        $result = Arrays::head_option($input);

        $this->assertNull($result);
    }

    public function testHeadOptionWithStringKeys(): void
    {
        $input  = ['first' => 'a', 'second' => 'b', 'third' => 'c'];
        $result = Arrays::head_option($input);

        $this->assertSame('a', $result);
    }
}
