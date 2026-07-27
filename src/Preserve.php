<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 入力の型をそのまま返すことを表すマーカーです。
 *
 * `ShapeOp` の型パラメータ `I` / `E` に渡して使います。`I` に渡すと入力のキー型を保存し、
 * `E` に渡すと入力の値型を保存します。
 *
 * インスタンス化しません。型パラメータの位置に置くためだけに存在します。
 *
 * @internal
 */
final class Preserve
{
}
