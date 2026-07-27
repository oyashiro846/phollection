<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class WindowedTest extends TestCase
{
    public function testWindowedWithDefaultStepReturnsFullWindows(): void
    {
        $input = [1, 2, 3, 4, 5];

        $result = windowed($input, 3);

        $this->assertSame([[1, 2, 3], [2, 3, 4], [3, 4, 5]], $result);
    }

    public function testWindowedWithStepSkipsStartPositions(): void
    {
        $input = [1, 2, 3, 4, 5];

        $result = windowed($input, 3, 2);

        $this->assertSame([[1, 2, 3], [3, 4, 5]], $result);
    }

    public function testWindowedWithPartialReturnsTrailingIncompleteWindow(): void
    {
        $input = [1, 2, 3, 4, 5];

        $result = windowed($input, 3, 2, true);

        $this->assertSame([[1, 2, 3], [3, 4, 5], [5]], $result);
    }

    public function testWindowedWithSizeGreaterThanCountReturnsEmpty(): void
    {
        $input = [1, 2];

        $result = windowed($input, 3);

        $this->assertSame([], $result);
    }

    public function testWindowedWithSizeGreaterThanCountAndPartialReturnsWindowsFromEachStart(): void
    {
        $input = [1, 2];

        $result = windowed($input, 3, 1, true);

        // 開始位置が件数未満なら窓を作るため、開始位置 1 の [2] も含まれる（Kotlin の windowed と同じ）
        $this->assertSame([[1, 2], [2]], $result);
    }

    public function testWindowedOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $this->assertSame([], windowed($input, 3));
        $this->assertSame([], windowed($input, 3, 1, true));
    }

    public function testWindowedWithSizeOneReturnsSingletonWindows(): void
    {
        $input = [1, 2, 3];

        $result = windowed($input, 1);

        $this->assertSame([[1], [2], [3]], $result);
    }

    public function testWindowedWithStepGreaterThanSizeSkipsElements(): void
    {
        $input = [1, 2, 3, 4, 5];

        $result = windowed($input, 2, 3);

        $this->assertSame([[1, 2], [4, 5]], $result);
    }

    public function testWindowedWithPartialWhenLastWindowEndsExactlyDoesNotAppendEmptyWindow(): void
    {
        $input = [1, 2, 3, 4];

        $result = windowed($input, 2, 2, true);

        // 開始位置 4 は件数と同じで窓を作らないため、末尾に空の窓は付かない
        $this->assertSame([[1, 2], [3, 4]], $result);
    }

    public function testWindowedWithZeroSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$size は 1 以上である必要があります。');

        windowed([1, 2, 3], 0);
    }

    public function testWindowedWithNegativeSizeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$size は 1 以上である必要があります。');

        windowed([1, 2, 3], -1);
    }

    public function testWindowedWithZeroStepThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$step は 1 以上である必要があります。');

        windowed([1, 2, 3], 2, 0);
    }

    public function testWindowedWithNegativeStepThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$step は 1 以上である必要があります。');

        windowed([1, 2, 3], 2, -1);
    }

    public function testWindowedOnAssocInputPreservesKeysInEachWindow(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = windowed($input, 2);

        $this->assertSame([
            ['alice' => 20, 'bob' => 17],
            ['bob' => 17, 'carol' => 23],
        ], $result);
    }

    public function testWindowedOnAssocInputWithListModeReindexesEachWindow(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = windowed($input, 2, 1, false, Mode::MODE_LIST);

        $this->assertSame([[20, 17], [17, 23]], $result);
    }

    public function testWindowedOnListInputWithAssocModePreservesIndexes(): void
    {
        $input = [1, 2, 3];

        $result = windowed($input, 2, 1, false, Mode::MODE_ASSOC);

        $this->assertSame([
            [0 => 1, 1 => 2],
            [1 => 2, 2 => 3],
        ], $result);
    }

    public function testWindowedDoesNotModifyInput(): void
    {
        $input = ['alice' => 20, 'bob' => 17, 'carol' => 23];

        windowed($input, 2, 1, true, Mode::MODE_LIST);

        $this->assertSame(['alice' => 20, 'bob' => 17, 'carol' => 23], $input);
    }
}
