<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection\Types;

use Oyashiro846\Phollection\Mode;

use function Oyashiro846\Phollection\filter;
use function PHPStan\Testing\assertType;

/**
 * カリー化した filter の型アサーション。
 *
 * PHPUnit のテストではなく PHPStan に検査させるためのファイル。推論結果が期待値と変われば
 * (広がっても狭まっても) level 10 が落ちる。
 */
function assertFilterTypes(): void
{
    /** @var list<int> $list */
    $list = [1, 2, 3];
    /** @var array<string, int> $assoc */
    $assoc = ['a' => 1, 'b' => 2];

    $cb = static fn (int $v, int|string $k): bool => $v > 1;

    assertType('list<int>', filter($cb)($list));
    assertType('array<string, int>', filter($cb)($assoc));
    assertType('list<int>', filter($cb, Mode::MODE_LIST)($assoc));
    assertType('array<int<0, max>, int>', filter($cb, Mode::MODE_ASSOC)($list));

    // 引数を 1 つしか宣言しないクロージャでも値型が保たれる
    // (内部関数の first-class callable は引数の数が厳密なので渡せない)
    $unary = static fn (int $v): bool => $v > 1;

    assertType('list<int>', filter($unary)($list));
    assertType('array<string, int>', filter($unary)($assoc));
}
