<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 連続する要素を $callback の戻り値が等しい範囲ごとにまとめた配列の配列を返します。
 *
 * PHP 標準の `array_chunk` が指定サイズで等分するのに対し、 本関数は隣接する要素間で
 * `$callback` の結果が変わる位置で chunk を切ります。 全要素数は保存され、 順序も維持されます。
 *
 * 例:
 * ```
 * chunk_by([1, 1, 2, 3, 3, 1], fn (int $v): int => $v);
 * // => [[1, 1], [2], [3, 3], [1]]
 * ```
 *
 * @template V
 * @template R
 *
 * @param list<V> $input  対象の配列 (list 前提。 associative array は順序保証が無いため非対応)
 * @param callable(V): R $callback  各要素を chunk キーに変換する。 戻り値は `===` で比較される。
 * @return list<non-empty-list<V>>
 */
function chunk_by(array $input, callable $callback): array
{
    $chunks  = [];
    $prevKey = null;

    foreach ($input as $value) {
        $key = $callback($value);

        if ($chunks !== [] && $prevKey === $key) {
            $chunks[\count($chunks) - 1][] = $value;
        } else {
            $chunks[] = [$value];
            $prevKey  = $key;
        }
    }

    return $chunks;
}
