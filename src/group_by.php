<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * コールバックで指定した分類キーごとに配列をグループ化します。
 *
 * @template K of array-key
 * @template V
 * @template G of array-key
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): G $classifier 分類キーを返す関数（第1引数: 値, 第2引数: キー）
 * @return array<G, list<V>|array<K, V>>
 * @phpstan-return ($input is list<V> ? array<G, list<V>> : array<G, array<K, V>>)
 */
function group_by(array $input, callable $classifier): array
{
    $result = [];
    $isList = array_is_list($input);

    foreach ($input as $key => $value) {
        $groupKey = $classifier($value, $key);

        $result[$groupKey] ??= [];

        if ($isList) {
            $result[$groupKey][] = $value;
        } else {
            $result[$groupKey][$key] = $value;
        }
    }

    return $result;
}
