<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 条件に一致する最初の要素の値を取得します。先頭から短絡評価で探索し、
 * 一致した時点で以降の要素には $callback を呼びません。一致する要素がない場合は null を返します。
 *
 * 注意: 一致した要素が null の場合と、一致する要素がない場合を区別できません。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback 一致条件 (値, キーの順で渡される)
 * @return V|null 一致した要素の値、または見つからない場合は null
 */
function find_option(array $input, callable $callback): mixed
{
    foreach ($input as $key => $value) {
        if ($callback($value, $key)) {
            return $value;
        }
    }

    return null;
}
