<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列の先頭から $callback が真である間の要素を取り出します。
 *
 * 先頭から評価し、初めて $callback が偽になった時点で境界を確定して評価を打ち切ります
 * (それ以降の要素には $callback を呼びません)。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback 継続条件（第1引数: 値, 第2引数: キー）
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V>:
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function take_while(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [];

    foreach ($input as $key => $value) {
        if (!$callback($value, $key)) {
            break;
        }

        if ($mode === Mode::MODE_LIST) {
            $result[] = $value;
        } else {
            $result[$key] = $value;
        }
    }

    return $result;
}
