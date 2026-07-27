<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 連続する要素を $callback の戻り値が等しい範囲ごとにまとめた配列の配列を返します。
 *
 * PHP 標準の `array_chunk` が指定サイズで等分するのに対し、 本関数は隣接する要素間で
 * `$callback` の結果が変わる位置で chunk を切ります。 全要素数は保存され、 順序も維持されます。
 *
 * PHP の配列は挿入順を保持するため associative array でも「隣接する要素」は定義できます。
 * よって入力は list でも associative array でも構いません。 chunk の内部のキーの扱いは
 * $mode に従い、 `MODE_LIST` なら 0 始まりの連番へ振り直し、 `MODE_ASSOC` なら元のキーを維持します。
 * 外側 (chunk の並び) は $mode によらず常に list です。
 *
 * 例:
 * ```
 * chunk_by([1, 1, 2, 3, 3, 1], fn (int $v): int => $v);
 * // => [[1, 1], [2], [3, 3], [1]]
 *
 * chunk_by(['a' => 1, 'b' => 1, 'c' => 2], fn (int $v): int => $v);
 * // => [['a' => 1, 'b' => 1], ['c' => 2]]
 * ```
 *
 * @template K of array-key
 * @template V
 * @template R
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): R $callback  各要素を chunk キーに変換する。 戻り値は `===` で比較される。
 * 空の chunk は作らないが、 assoc 側は `non-empty-array<never, never>` が
 * `chunk_by([], ...)` の呼び出しで unresolvable になるため `non-empty` を付けていない。
 *
 * @return list<non-empty-list<V>>|list<array<K, V>>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<non-empty-list<V>> :
 *     ($mode is Mode::MODE_ASSOC ? list<array<K, V>> :
 *       ($input is list<V> ? list<non-empty-list<V>> :
 *         list<array<K, V>>
 *  )))
 */
function chunk_by(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $chunks       = [];
    $prevChunkKey = null;

    foreach ($input as $key => $value) {
        $chunkKey = $callback($value, $key);

        if ($chunks !== [] && $prevChunkKey === $chunkKey) {
            $last = \count($chunks) - 1;

            if ($mode === Mode::MODE_ASSOC) {
                $chunks[$last][$key] = $value;
            } else {
                $chunks[$last][] = $value;
            }
        } elseif ($mode === Mode::MODE_ASSOC) {
            $chunks[] = [$key => $value];
        } else {
            $chunks[] = [$value];
        }

        $prevChunkKey = $chunkKey;
    }

    return $chunks;
}
