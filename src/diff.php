<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * $input のうち $other に含まれない値だけを残します（差集合）。
 *
 * PHP の {@see array_diff()} のラッパーとして作られていますが、比較方法を意図的に変えています。
 * array_diff() は要素を文字列化して比較するため、0 と '0'、1 と '1'、true と '1' を
 * 同一視し、配列やオブジェクトを含む配列では警告や誤判定になります。本ライブラリは
 * strict_types + PHPStan level 10 の型安全志向のため array_diff() には委譲せず、
 * in_array() の厳密比較（===）モードで判定します。
 *
 * また、Scala の diff（multiset の差。要素の出現回数ぶんだけ取り除く）とは異なり、
 * array_diff() と同じ集合としての差を返します。$input に重複した値がある場合、
 * $other に含まれる値は重複分もすべて除去されます。
 *
 * $other のキーは見ず、値の集合としてのみ使用します。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param list<mixed>|array<array-key, mixed> $other 除外する値を持つ配列（キーは見ない）
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function diff(array $input, array $other, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [];

    foreach ($input as $key => $value) {
        if (\in_array($value, $other, true)) {
            continue;
        }

        if ($mode === Mode::MODE_LIST) {
            $result[] = $value;
        } else {
            $result[$key] = $value;
        }
    }

    return $result;
}
