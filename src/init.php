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
 * @phpstan-param ($mode is Mode::MODE_LIST ? list<V> :
 *   ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *     list<V>|array<K, V>
 * )) $input
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function init(array $input, Mode $mode = Mode::MODE_AUTO): array
{
    return \array_slice(
        $input,
        0,
        -1,
        Mode::check_mode($mode, $input) === Mode::MODE_ASSOC,
    );
}
