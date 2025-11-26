<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class UniqueTest extends TestCase
{
    public function testUniqueNumericListWithoutFlagsWithAutoMode(): void
    {
        $result = Arrays::unique([3, 1, 4, 1, 5, 9, 2]);

        $this->assertSame([3, 1, 4, 5, 9, 2], $result);
    }

    public function testUniqueNumericListWithAutoMode(): void
    {
        $result = Arrays::unique([3, 1, 4, 1, 5, 9, 2], SORT_NUMERIC);

        $this->assertSame([3, 1, 4, 5, 9, 2], $result);
    }

    public function testUniqueNumericsAssocWithAutoMode(): void
    {
        $result = Arrays::unique(["a" => 3, "b" => 1, "c" => 4, "d" => 1, "e" => 5], SORT_NUMERIC);

        $this->assertSame(["a" => 3, "b" => 1, "c" => 4, "e" => 5], $result);
    }

    public function testUniqueNumericListWithAssocMode(): void
    {
        $result = Arrays::unique([3, 1, 4, 1, 5, 9, 2], SORT_NUMERIC, Mode::MODE_ASSOC);

        $this->assertSame([0 => 3, 1 => 1, 2 => 4, 4 => 5, 5 => 9, 6 => 2], $result);
    }

    public function testUniqueStringListWithListMode(): void
    {
        $result = Arrays::unique(["3", "1", "4", "1", "5", "9", "2"], SORT_STRING, Mode::MODE_LIST);

        $this->assertSame(["3", "1", "4", "5", "9", "2"], $result);
    }

    public function testUniqueMixedTypeListWithoutFlagsWithAutoMode(): void
    {
        $result = Arrays::unique([3, 1, "4", "1", 5, 9, "2", 6, 5.0, 3, "5"]);

        $this->assertSame([3, 1, "4", 5, 9, "2", 6], $result);
    }
}
