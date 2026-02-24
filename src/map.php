<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * @template K of array-key
 * @template V
 * @template E
 *
 * @param list<V>|array<K, V> $input
 * @param callable(V, K): E $callback
 *
 * @return list<E>|array<K, E>
 *
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<E> :
 *   ($mode is Mode::MODE_ASSOC ? array<K, E> :
 *     ($input is list<V> ? list<E> :
 *       array<K, E>
 *     )
 *   )
 * )
 */
function map(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $keys   = array_keys($input);
    $result = array_map($callback, $input, $keys);

    if ($mode === Mode::MODE_ASSOC) {
        return array_combine($keys, $result);
    }

    return $result;
}
