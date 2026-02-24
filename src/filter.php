<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列を条件でフィルタします。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback フィルターする条件
 * @phpstan-param ($mode is Mode::MODE_LIST ? list<V> :
 *   ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *     list<V>|array<K, V>
 * )) $input
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V>:
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function filter(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    if ($mode === Mode::MODE_ASSOC) {
        return array_filter($input, $callback, ARRAY_FILTER_USE_BOTH);
    }

    $result = [];

    foreach ($input as $key => $value) {
        if ($callback($value, $key)) {
            $result[] = $value;
        }
    }

    return $result;
}
