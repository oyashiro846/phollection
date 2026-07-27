<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class DiffTest extends TestCase
{
    public function testDiffOnBothEmptyArraysReturnsEmpty(): void
    {
        /** @var list<int> $input */
        $input = [];
        /** @var list<int> $other */
        $other = [];

        $result = diff($input, $other);

        $this->assertSame([], $result);
    }

    public function testDiffWithEmptyOtherReturnsAllElements(): void
    {
        $input = [1, 2, 3];
        /** @var list<int> $other */
        $other = [];

        $result = diff($input, $other);

        $this->assertSame([1, 2, 3], $result);
    }

    public function testDiffOnEmptyInputReturnsEmpty(): void
    {
        /** @var list<int> $input */
        $input = [];
        $other = [1, 2, 3];

        $result = diff($input, $other);

        $this->assertSame([], $result);
    }

    public function testDiffRemovesSomeElements(): void
    {
        $input = [1, 2, 3, 4];
        $other = [2, 4];

        $result = diff($input, $other);

        $this->assertSame([1, 3], $result);
    }

    public function testDiffRemovesAllElements(): void
    {
        $input = [1, 2, 3];
        $other = [1, 2, 3, 4];

        $result = diff($input, $other);

        $this->assertSame([], $result);
    }

    public function testDiffDistinguishesZeroAndStringZero(): void
    {
        $input = [0, '0', 1, '1'];
        $other = ['0', '1'];

        $result = diff($input, $other);

        // array_diff() なら '0'/'1' が 0/1 と同一視され全件除去されるが、
        // 厳密比較では型の異なる 0 と 1 は残る
        $this->assertSame([0, 1], $result);
    }

    public function testDiffDistinguishesTrueAndStringOne(): void
    {
        $input = [true, '1', false];
        $other = ['1'];

        $result = diff($input, $other);

        // array_diff() なら true が '1' と同一視され除去されるが、
        // 厳密比較では型の異なる true は残る
        $this->assertSame([true, false], $result);
    }

    public function testDiffRemovesAllDuplicatesOfMatchedValue(): void
    {
        $input = [1, 2, 2, 3, 2];
        $other = [2];

        $result = diff($input, $other);

        $this->assertSame([1, 3], $result);
    }

    public function testDiffOnAssocInputPreservesKeys(): void
    {
        $input = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];
        $other = [2];

        $result = diff($input, $other);

        $this->assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function testDiffOnAssocInputWithListModeReindexes(): void
    {
        $input = [
            'a' => 10,
            'b' => 20,
            'c' => 30,
        ];
        $other = [20];

        $result = diff($input, $other, Mode::MODE_LIST);

        $this->assertSame([10, 30], $result);
        $this->assertSame([0, 1], array_keys($result));
    }

    public function testDiffWithNullValues(): void
    {
        $input = [1, null, 2, null];
        $other = [null];

        $result = diff($input, $other);

        $this->assertSame([1, 2], $result);
    }

    public function testDiffDoesNotMutateInput(): void
    {
        $input = [1, 2, 3, 4];
        $other = [2, 4];

        diff($input, $other);

        $this->assertSame([1, 2, 3, 4], $input);
    }

    public function testDiffDoesNotMutateOther(): void
    {
        $input = [1, 2, 3, 4];
        $other = [2, 4];

        diff($input, $other);

        $this->assertSame([2, 4], $other);
    }
}
