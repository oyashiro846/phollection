<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class ChunkTest extends TestCase
{
    public function testChunkOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = chunk($input, 3);

        $this->assertSame([], $result);
    }

    public function testChunkOnDivisibleListSplitsEvenly(): void
    {
        $input = [1, 2, 3, 4, 5, 6];

        $result = chunk($input, 2);

        $this->assertSame([[1, 2], [3, 4], [5, 6]], $result);
    }

    public function testChunkOnIndivisibleListKeepsRemainder(): void
    {
        $input = [1, 2, 3, 4, 5];

        $result = chunk($input, 2);

        $this->assertSame([[1, 2], [3, 4], [5]], $result);
    }

    public function testChunkWithSizeGreaterThanCountReturnsSingleChunk(): void
    {
        $input = [1, 2, 3];

        $result = chunk($input, 10);

        $this->assertSame([[1, 2, 3]], $result);
    }

    public function testChunkWithSizeOneReturnsSingletonChunks(): void
    {
        $input = [1, 2, 3];

        $result = chunk($input, 1);

        $this->assertSame([[1], [2], [3]], $result);
    }

    public function testChunkWithZeroSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$size は 1 以上である必要があります。');

        chunk([1, 2, 3], 0);
    }

    public function testChunkWithNegativeSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$size は 1 以上である必要があります。');

        chunk([1, 2, 3], -1);
    }

    public function testChunkOnAssocInputPreservesKeysInEachChunk(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = chunk($input, 2);

        $this->assertSame([
            ['alice' => 20, 'bob' => 17],
            ['carol' => 23],
        ], $result);
    }

    public function testChunkOnAssocInputWithListModeReindexesEachChunk(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = chunk($input, 2, Mode::MODE_LIST);

        $this->assertSame([[20, 17], [23]], $result);
    }

    public function testChunkOnListInputWithAssocModePreservesIndexes(): void
    {
        $input = [1, 2, 3];

        $result = chunk($input, 2, Mode::MODE_ASSOC);

        $this->assertSame([
            [0 => 1, 1 => 2],
            [2 => 3],
        ], $result);
    }

    public function testChunkDoesNotModifyInput(): void
    {
        $input = ['alice' => 20, 'bob' => 17];

        chunk($input, 1, Mode::MODE_LIST);

        $this->assertSame(['alice' => 20, 'bob' => 17], $input);
    }
}
