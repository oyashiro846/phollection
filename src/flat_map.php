<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 各要素をコールバックで配列に変換し、その結果を順序を保ったまま 1 段だけ平坦化します。
 *
 * 平坦化するのは $callback が返した配列 1 段だけです。
 * その中にさらに配列が入っていても展開せず、値としてそのまま格納します。
 *
 * MODE_LIST では $callback の戻り配列のキーを捨て、結果全体を 0 始まりの連番に振り直します。
 * MODE_ASSOC では入力側のキーではなく $callback の戻り配列のキーを採用します。
 * このとき同じキーが複数回現れた場合は、後に現れたものが前のものを上書きします。
 *
 * @template K of array-key
 * @template V
 * @template R of array-key
 * @template E
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): (list<E>|array<R, E>) $callback 各要素を配列に変換する関数（第1引数: 値, 第2引数: キー）
 * @return list<E>|array<R, E>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<E> :
 *    ($mode is Mode::MODE_ASSOC ? array<R, E> :
 *      ($input is list<V> ? list<E> :
 *        array<R, E>
 * )))
 */
function flat_map(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [];

    foreach ($input as $key => $value) {
        foreach ($callback($value, $key) as $innerKey => $element) {
            if ($mode === Mode::MODE_LIST) {
                $result[] = $element;
            } else {
                $result[$innerKey] = $element;
            }
        }
    }

    return $result;
}
