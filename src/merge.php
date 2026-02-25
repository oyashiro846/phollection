<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 任意の数の配列を受け取り、1つの配列にして返します
 *
 * @template K of array-key
 * @template V
 *
 * @param array<list<V>|array<K, V>> $inputs
 * @param Mode $mode MODE_AUTOのときは配列・連想配列の区別なくマージ。MODE_LISTのときは配列としてマージ。MODE_ASSOCのときはキーを維持して連想配列としてマージ。
 * @return list<V>|array<K, V>
 *
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V>:
 *       ($inputs is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function merge(
    array $inputs,
    Mode $mode = Mode::MODE_AUTO,
): array {
    if (\count($inputs) <= 0) {
        return [];
    }

    if ($mode === Mode::MODE_LIST) {
        $result = [];

        foreach ($inputs as $input) {
            foreach ($input as $key => $value) {
                $result[] = $value;
            }
        }

        return $result;
    }

    if ($mode === Mode::MODE_ASSOC) {
        return array_replace(...$inputs);
    }

    return array_merge(...$inputs);
}
