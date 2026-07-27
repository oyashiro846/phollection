<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列を述語で 2 つに分割します。
 *
 * 添字 0 が述語を満たした要素、添字 1 が満たさなかった要素です。
 * どちらの側も元の並び順を保ち、2 つを合わせると入力の全要素になります。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback 振り分ける条件（true なら添字 0 側、false なら添字 1 側）
 * @return array{0: list<V>|array<K, V>, 1: list<V>|array<K, V>}
 * @phpstan-return ($mode is Mode::MODE_LIST ? array{0: list<V>, 1: list<V>} :
 *     ($mode is Mode::MODE_ASSOC ? array{0: array<K, V>, 1: array<K, V>} :
 *       ($input is list<V> ? array{0: list<V>, 1: list<V>} :
 *         array{0: array<K, V>, 1: array<K, V>}
 *  )))
 */
function partition(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [[], []];

    foreach ($input as $key => $value) {
        $side = $callback($value, $key) ? 0 : 1;

        if ($mode === Mode::MODE_LIST) {
            $result[$side][] = $value;
        } else {
            $result[$side][$key] = $value;
        }
    }

    return $result;
}
