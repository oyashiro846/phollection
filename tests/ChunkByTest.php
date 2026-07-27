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

    public function testChunkByOnAssocWithAutoModePreservesKeys(): void
    {
        $this->assertSame(
            [['a' => 1, 'b' => 1], ['c' => 2], ['d' => 3, 'e' => 3]],
            chunk_by(
                ['a' => 1, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 3],
                fn (int $v): int => $v,
            ),
        );
    }

    public function testChunkByOnAssocWithListModeDropsKeys(): void
    {
        $this->assertSame(
            [[1, 1], [2], [3, 3]],
            chunk_by(
                ['a' => 1, 'b' => 1, 'c' => 2, 'd' => 3, 'e' => 3],
                fn (int $v): int => $v,
                Mode::MODE_LIST,
            ),
        );
    }

    public function testChunkByOnListWithAssocModeKeepsOriginalIndexes(): void
    {
        // MODE_ASSOC では chunk の中でも元の添字を維持し、 詰め直さない。
        $this->assertSame(
            [[0 => 1, 1 => 1], [2 => 2], [3 => 3, 4 => 3]],
            chunk_by([1, 1, 2, 3, 3], fn (int $v): int => $v, Mode::MODE_ASSOC),
        );
    }

    public function testChunkByPassesKeyAsSecondArgument(): void
    {
        // キーの頭文字が変わる位置で chunk を切る。
        $this->assertSame(
            [['ax' => 1, 'ay' => 2], ['bx' => 3], ['cx' => 4, 'cy' => 5]],
            chunk_by(
                ['ax' => 1, 'ay' => 2, 'bx' => 3, 'cx' => 4, 'cy' => 5],
                fn (int $v, string $key): string => $key[0],
            ),
        );
    }

    public function testChunkByOnEmptyArrayReturnsEmptyForEveryMode(): void
    {
        /** @var array<string, int> $empty */
        $empty = [];

        $this->assertSame([], chunk_by($empty, fn (int $v): int => $v, Mode::MODE_AUTO));
        $this->assertSame([], chunk_by($empty, fn (int $v): int => $v, Mode::MODE_LIST));
        $this->assertSame([], chunk_by($empty, fn (int $v): int => $v, Mode::MODE_ASSOC));
    }

    public function testChunkByOnEmptyArrayLiteralReturnsEmptyForEveryMode(): void
    {
        // 型注釈の無い空配列リテラルでも PHPStan が戻り値の型を解決できること
        // (assoc 側に non-empty-array を付けると unresolvableReturnType になる) の回帰テスト。
        $this->assertSame([], chunk_by([], fn ($v) => $v));
        $this->assertSame([], chunk_by([], fn ($v) => $v, Mode::MODE_AUTO));
        $this->assertSame([], chunk_by([], fn ($v) => $v, Mode::MODE_LIST));
        $this->assertSame([], chunk_by([], fn ($v) => $v, Mode::MODE_ASSOC));
    }
}
