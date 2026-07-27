<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列の先頭から $n 件を除外します。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param int $n 除外する件数（0 以下ならすべて、件数以上なら空配列）
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function drop(array $input, int $n, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = \array_slice($input, max($n, 0), null, true);

    return $mode === Mode::MODE_LIST ? array_values($result) : $result;
}
