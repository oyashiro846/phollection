<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列の先頭から $callback が真である間の要素を捨て、初めて偽になった要素以降をすべて返します。
 *
 * 境界が確定した後の要素は $callback の結果に関わらずすべて残します。
 * 境界より後ろで $callback が再び真になっても取り除かない点が filter() と異なります。
 * 境界確定後は $callback を呼びません。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback 除外条件（第1引数: 値, 第2引数: キー）
 * @return list<V>|array<K, V>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
 *     ($mode is Mode::MODE_ASSOC ? array<K, V>:
 *       ($input is list<V> ? list<V> :
 *         array<K, V>
 *  )))
 */
function drop_while(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result   = [];
    $dropping = true;

    foreach ($input as $key => $value) {
        if ($dropping) {
            if ($callback($value, $key)) {
                continue;
            }

            $dropping = false;
        }

        if ($mode === Mode::MODE_LIST) {
            $result[] = $value;
        } else {
            $result[$key] = $value;
        }
    }

    return $result;
}
