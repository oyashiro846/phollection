<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * $selector が返す比較キーの昇順で配列をソートします。
 *
 * 比較キー同士の比較には宇宙船演算子 `<=>` を使います。
 * PHP 8.0 以降の usort / uasort は安定ソートであるため、比較キーが同値の要素は
 * 入力順を維持します（安定ソート）。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): mixed $selector 比較キーを返す関数（第1引数: 値, 第2引数: キー）
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V>:
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function sort_by(array $input, callable $selector, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $entries = [];

    foreach ($input as $key => $value) {
        $entries[] = [
            'key'      => $key,
            'value'    => $value,
            'sort_key' => $selector($value, $key),
        ];
    }

    usort($entries, fn (array $a, array $b): int => $a['sort_key'] <=> $b['sort_key']);

    if ($mode === Mode::MODE_ASSOC) {
        $result = [];

        foreach ($entries as $entry) {
            $result[$entry['key']] = $entry['value'];
        }

        return $result;
    }

    return array_map(fn (array $entry) => $entry['value'], $entries);
}
