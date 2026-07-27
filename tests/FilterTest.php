<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function test_filter_list_auto_mode(): void
    {
        $input = [1, 2, 3, 4];

        $result = filter(
            fn (int $v): bool => $v % 2 === 0,
        )($input);

        $this->assertSame([2, 4], $result);
    }

    public function test_filter_list_explicit_list_mode(): void
    {
        $input = [1, 2, 3, 4];

        $result = filter(
            fn (int $v): bool => $v > 2,
            Mode::MODE_LIST,
        )($input);

        $this->assertSame([3, 4], $result);
    }

    public function test_filter_assoc_auto_mode(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = filter(
            fn (int $age, string $name): bool => $age >= 20,
        )($input);

        $this->assertSame(['alice' => 20, 'carol' => 23], $result);
    }

    public function test_filter_assoc_explicit_assoc_mode(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = filter(
            fn (int $age): bool => $age >= 18,
            Mode::MODE_ASSOC,
        )($input);

        $this->assertSame(['alice' => 20], $result);
    }

    public function testFilterAssocWithListModeRenumbersKeys(): void
    {
        // MODE_LIST は入力が assoc でもキーを捨て、 0 始まり連番の完全な list を返す。
        $input = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = filter(
            static fn (int $v): bool => $v > 1,
            Mode::MODE_LIST,
        )($input);

        $this->assertSame([2, 3], $result);
    }

    public function testFilterListWithAssocModeKeepsOriginalIndexes(): void
    {
        // MODE_ASSOC は入力が list でもキーを維持するため、 要素が減っても添字を詰めない。
        $input = [1, 2, 3];

        $result = filter(
            static fn (int $v): bool => $v > 1,
            Mode::MODE_ASSOC,
        )($input);

        $this->assertSame([1 => 2, 2 => 3], $result);
    }

    public function test_filter_list_with_key_aware_callback(): void
    {
        $input = [10, 20, 30];

        $result = filter(
            fn (int $value, int $index): bool => $index % 2 === 0,
            Mode::MODE_LIST,
        )($input);

        $this->assertSame([10, 30], $result);
    }

    public function testFilterReturnsCallable(): void
    {
        // パイプ演算子の右辺に置けること = 引数 1 つの callable として呼べることを確かめる。
        // assertIsCallable は PHPStan が「常に true」と判定して level 10 で落ちるため、
        // callable を要求する call_user_func 経由で実際に呼び出して確認する。
        $result = \call_user_func(
            filter(static fn (int $v): bool => $v % 2 === 0),
            [1, 2, 3, 4],
        );

        $this->assertSame([2, 4], $result);
    }

    public function testFilterOperationIsReusableForMultipleArrays(): void
    {
        $op = filter(static fn (int $v): bool => $v % 2 === 0);

        $this->assertSame([2, 4], $op([1, 2, 3, 4]));
        $this->assertSame(['b' => 20], $op(['a' => 5, 'b' => 20]));
    }

    #[RequiresPhp('>= 8.5')]
    public function testFilterWorksWithPipeOperator(): void
    {
        // パイプ演算子は PHP 8.4 ではパース時に構文エラーになる。
        // このテストは 8.5 でのみ実行されるが、ファイル全体のパースは 8.4 でも行われるため eval で包む。
        $result = eval('return [1, 2, 3, 4] |> \Oyashiro846\Phollection\filter(static fn (int $v): bool => $v % 2 === 0);');

        $this->assertSame([2, 4], $result);
    }
}
