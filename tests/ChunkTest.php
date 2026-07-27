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

    /**
     * 空配列リテラルを変数に入れず直接渡す呼び出しです。
     *
     * 戻り値の assoc 側を non-empty-array<K, V> にすると non-empty-array<never, never> が解決できず、
     * この形の呼び出しだけが PHPStan で落ちます。その回帰を捕まえるために各モードで呼びます。
     */
    public function testChunkOnEmptyArrayLiteralReturnsEmptyInEveryMode(): void
    {
        $this->assertSame([], chunk([], 2));
        $this->assertSame([], chunk([], 2, Mode::MODE_LIST));
        $this->assertSame([], chunk([], 2, Mode::MODE_ASSOC));
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

        chunk([1, 2, 3], $this->invalidPositiveInt(0));
    }

    public function testChunkWithNegativeSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$size は 1 以上である必要があります。');

        chunk([1, 2, 3], $this->invalidPositiveInt(-1));
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

    /**
     * 実行時の防御を検証するために不正な $size を作ります。
     *
     * chunk は $size に positive-int を宣言しているため 0 や負数を直接渡すと静的解析で弾かれますが、
     * phpdoc の型は実行時には強制されないので、ライブラリ利用者は不正値を渡せてしまいます。
     * その状況を再現するため、int を経由して positive-int として扱わせます。
     *
     * @return positive-int
     */
    private function invalidPositiveInt(int $value): int
    {
        /** @var positive-int $widened */
        $widened = $value;

        return $widened;
    }
}
