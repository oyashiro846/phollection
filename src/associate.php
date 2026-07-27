<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * コールバックが返す [キー, 値] のペアから新しい連想配列を構築します。
 *
 * @template K of array-key
 * @template V
 * @template G of array-key
 * @template E
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): array{0: G, 1: E} $transform 新しいキーと値のペアを返す関数（第1引数: 値, 第2引数: キー）
 * @return array<G, E> 元のキーは捨てられ、$transform が返したキーだけが使われる。同一キーが衝突した場合は後勝ちになる
 */
function associate(array $input, callable $transform): array
{
    $result = [];

    foreach ($input as $key => $value) {
        [$newKey, $newValue] = $transform($value, $key);

        $result[$newKey] = $newValue;
    }

    return $result;
}
