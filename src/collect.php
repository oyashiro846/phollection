<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列を変換しつつ、null になった要素を取り除きます。
 *
 * @template K of array-key
 * @template V
 * @template E
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): ?E $callback $callback フィルターする条件
 * @phpstan-param ($mode is Mode::MODE_LIST ? list<V> :
 *    ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *      list<V>|array<K, V>
 *  )) $input
 * @return list<E>|array<K, E>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<E> :
 *    ($mode is Mode::MODE_ASSOC ? array<K, E> :
 *      ($input is list<V> ? list<E> :
 *        array<K, E>
 * )))
 */
function collect(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [];

    foreach ($input as $key => $value) {
        $element = $callback($value, $key);

        if (!\is_null($element)) {
            if ($mode === Mode::MODE_LIST) {
                $result[] = $element;
            } elseif ($mode === Mode::MODE_ASSOC) {
                $result[$key] = $element;
            }
        }
    }

    return $result;
}
