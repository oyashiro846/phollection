<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列の最後の要素を除いたすべての要素を返します。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function init(array $input, Mode $mode = Mode::MODE_AUTO): array
{
    $mode  = Mode::check_mode($mode, $input);
    $slice = \array_slice($input, 0, -1, true);

    return $mode === Mode::MODE_LIST ? array_values($slice) : $slice;
}
