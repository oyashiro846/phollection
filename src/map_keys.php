<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列のキーを変換します。値は保持されます。
 *
 * 注意: 複数のキーが同じ値に変換された場合、後のエントリの値が前のエントリを上書きします。
 *
 * コールバックの引数順序について:
 * この関数は「キーの変換」が主目的のため、コールバックは (key, value) の順で受け取ります。
 * これは map() や filter() などの (value, key) 順とは異なります。
 *
 * ```php
 * // ✅ 正しい使い方: 第1引数がキー、第2引数が値
 * map_keys($arr, fn(string $key, int $value): string => strtoupper($key));
 *
 * // ❌ 間違い: map() と同じ引数順序を期待している
 * map_keys($arr, fn(int $value, string $key): string => strtoupper($key));
 * ```
 *
 * @template K of array-key
 * @template V
 * @template R of array-key
 *
 * @param array<K, V> $input 対象の配列
 * @param callable(K, V): R $callback キーを変換する関数（第1引数: キー, 第2引数: 値）
 * @return array<R, V> キーが変換された新しい配列
 */
function map_keys(array $input, callable $callback): array
{
    $result = [];

    foreach ($input as $key => $value) {
        $newKey          = $callback($key, $value);
        $result[$newKey] = $value;
    }

    return $result;
}
