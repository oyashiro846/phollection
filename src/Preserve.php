<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 入力の型をそのまま返すことを表すマーカーです。
 *
 * `ShapeOp` の型パラメータ `I` / `E` に渡して使います。`I` に渡すと入力のキー型を保存し、
 * `E` に渡すと入力の値型を保存します。
 *
 * 型パラメータの位置に置くためだけに存在するので、コンストラクタを private にして
 * インスタンス化を塞いでいます。
 *
 * @internal
 */
final class Preserve
{
    private function __construct()
    {
    }
}
