<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class LastOptionTest extends TestCase
{
    public function testLastOptionWithList(): void
    {
        $input  = [1, 2, 3];
        $result = Arrays::last_option($input);

        $this->assertSame(3, $result);
    }

    public function testLastOptionWithAssoc(): void
    {
        $input  = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = Arrays::last_option($input);

        $this->assertSame(3, $result);
    }

    public function testLastOptionWithNumericAssoc(): void
    {
        $input  = [10 => 'a', 20 => 'b', 30 => 'c'];
        $result = Arrays::last_option($input);

        $this->assertSame('c', $result);
    }

    public function testLastOptionWithSingleElement(): void
    {
        $input  = [42];
        $result = Arrays::last_option($input);

        $this->assertSame(42, $result);
    }

    public function testLastOptionWithSingleElementAssoc(): void
    {
        $input  = ['key' => 'value'];
        $result = Arrays::last_option($input);

        $this->assertSame('value', $result);
    }

    public function testLastOptionWithEmptyArray(): void
    {
        $input  = [];
        $result = Arrays::last_option($input);

        // @phpstan-ignore-next-line method.alreadyNarrowedType
        $this->assertNull($result);
    }

    public function testLastOptionWithNullValue(): void
    {
        $input  = [1, 2, null];
        $result = Arrays::last_option($input);

        $this->assertNull($result);
    }

    public function testLastOptionWithStringKeys(): void
    {
        $input  = ['first' => 'a', 'second' => 'b', 'third' => 'c'];
        $result = Arrays::last_option($input);

        $this->assertSame('c', $result);
    }
}
