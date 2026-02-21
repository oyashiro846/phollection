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
        $result = Arrays::unique(input: [3, 1, 4, 1, 5, 9, 2], flags: SORT_NUMERIC);

        $this->assertSame([3, 1, 4, 5, 9, 2], $result);
    }

    public function testUniqueNumericsAssocWithAutoMode(): void
    {
        $result = Arrays::unique(['a' => 3, 'b' => 1, 'c' => 4, 'd' => 1, 'e' => 5], flags: SORT_NUMERIC);

        $this->assertSame(['a' => 3, 'b' => 1, 'c' => 4, 'e' => 5], $result);
    }

    public function testUniqueNumericListWithAssocMode(): void
    {
        $result = Arrays::unique([3, 1, 4, 1, 5, 9, 2], flags: SORT_NUMERIC, mode: Mode::MODE_ASSOC);

        $this->assertSame([0 => 3, 1 => 1, 2 => 4, 4 => 5, 5 => 9, 6 => 2], $result);
    }

    public function testUniqueStringListWithListMode(): void
    {
        $result = Arrays::unique(['3', '1', '4', '1', '5', '9', '2'], flags: SORT_STRING, mode: Mode::MODE_LIST);

        $this->assertSame(['3', '1', '4', '5', '9', '2'], $result);
    }

    public function testUniqueMixedTypeListWithoutFlagsWithAutoMode(): void
    {
        $result = Arrays::unique([3, 1, '4', '1', 5, 9, '2', 6, 5.0, 3, '5']);

        $this->assertSame([3, 1, '4', 5, 9, '2', 6], $result);
    }

    public function testUniqueStringListWithStrictWithListMode(): void
    {
        $result = Arrays::unique(['3', '1', '4', '1', '5', '9', '2'], strict: true, mode: Mode::MODE_LIST);

        $this->assertSame(['3', '1', '4', '5', '9', '2'], $result);
    }

    public function testUniqueObjectWithStrictWithAutoMode(): void
    {
        $obj1     = new \stdClass();
        $obj1->id = 1;
        $obj2     = new \stdClass();
        $obj2->id = 1;
        $obj3     = $obj1;

        $result = Arrays::unique([$obj1, $obj2, $obj3], strict: true);

        $this->assertSame([$obj1, $obj2], $result);
    }

    public function testEmptyListWithStrictWithAutoMode(): void
    {
        $result = Arrays::unique([], true);

        $this->assertSame([], $result);
    }

    public function testSameValueListWithStrictWithAutoMode(): void
    {
        $result = Arrays::unique([1, 1, 1, 1, 1, 1], true);

        $this->assertSame([1], $result);
    }

    public function testNestedListWithStrictWithAutoMode(): void
    {
        $result = Arrays::unique([1, [1.25, 1.5, ['1.75']], [1.25, 1.5, ['1.75']], 2, ['value' => 2.5], ['value' => 2.5], 3]);

        $this->assertSame([1, [1.25, 1.5, ['1.75']], 2, ['value' => 2.5], 3], $result);
    }

    public function testListWithResourceWithStrictWithAutoMode(): void
    {
        $resource = fopen('php://memory', 'r+');

        if ($resource === false) {
            throw new \RuntimeException('Failed to open resource');
        }

        fwrite($resource, 'Hello, world!');

        $result = Arrays::unique([$resource, $resource], true);

        $this->assertSame([$resource], $result);

        fclose($resource);
    }

    public function testSingleValueListWithStrictWithAutoMode(): void
    {
        $result = Arrays::unique([1], strict: true);
        $this->assertSame([1], $result);
    }
}
