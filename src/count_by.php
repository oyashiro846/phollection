<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * コールバックが返す分類キーごとに要素の件数を集計します。
 *
 * @template K of array-key
 * @template V
 * @template G of array-key
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): G $classifier 分類キーを返す関数（第1引数: 値, 第2引数: キー）
 * @return array<G, positive-int> 分類キーごとの件数。キーの出現順は初出順を維持する
 */
function count_by(array $input, callable $classifier): array
{
    $result = [];

    foreach ($input as $key => $value) {
        $groupKey = $classifier($value, $key);

        $result[$groupKey] = ($result[$groupKey] ?? 0) + 1;
    }

    return $result;
}
