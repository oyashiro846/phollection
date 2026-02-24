<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列の一部を展開する
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function slice(
    array $input,
    int $offset,
    ?int $length = null,
    Mode $mode = Mode::MODE_AUTO,
): array {
    $mode = Mode::check_mode($mode, $input);

    return \array_slice($input, $offset, $length, $mode === Mode::MODE_ASSOC);
}
