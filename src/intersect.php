<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 2つの配列の共通要素を返します（積集合）。
 *
 * PHP の {@see array_intersect()} のラッパーです。
 * 第1配列に含まれる値のうち、第2配列にも存在するものだけを返します。
 * 比較は値の文字列表現で行われます（array_intersect の仕様）。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param list<V>|array<array-key, V> $other 比較対象の配列
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function intersect(array $input, array $other, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    if ($mode === Mode::MODE_LIST) {
        // LIST モードでは foreach で直接リストを構築（array_values の O(N) を回避）
        $result = [];

        foreach ($input as $value) {
            if (\in_array($value, $other, false)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    // ASSOC モードでは array_intersect を使用（キーを保持）
    return array_intersect($input, $other);
}
