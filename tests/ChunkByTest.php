<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class ChunkByTest extends TestCase
{
    public function testChunkByOnEmptyArrayReturnsEmpty(): void
    {
        $this->assertSame([], chunk_by([], fn ($v) => $v));
    }

    public function testChunkByWithSingleElementReturnsSingleChunk(): void
    {
        $this->assertSame([[42]], chunk_by([42], fn ($v) => $v));
    }

    public function testChunkByAllSameKeyReturnsSingleChunk(): void
    {
        $this->assertSame(
            [[1, 3, 5]],
            chunk_by([1, 3, 5], fn (int $v): int => $v % 2),
        );
    }

    public function testChunkByAlternatingKeySplitsAtBoundary(): void
    {
        $this->assertSame(
            [[1, 2], ['a', 'b'], [3]],
            chunk_by([1, 2, 'a', 'b', 3], fn ($v): bool => \is_int($v)),
        );
    }

    public function testChunkByMultipleKeysSplitsCorrectly(): void
    {
        $this->assertSame(
            [[1, 1], [2], [3, 3, 3], [1]],
            chunk_by([1, 1, 2, 3, 3, 3, 1], fn (int $v): int => $v),
        );
    }

    public function testChunkByPreservesOrderInChunks(): void
    {
        // 各 chunk 内では入力順を保つ。
        $this->assertSame(
            [[3, 1], [4, 2], [5]],
            chunk_by([3, 1, 4, 2, 5], fn (int $v): bool => $v % 2 !== 0),
        );
    }

    public function testChunkByDoesNotMutateInput(): void
    {
        $input = [1, 2, 'a'];
        $copy  = $input;

        chunk_by($input, fn ($v): bool => \is_int($v));

        $this->assertSame($copy, $input);
    }
}
