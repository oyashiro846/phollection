<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * partition の分類と変換を同時に行う版です。
 *
 * コールバックは「振り分け先」と「変換後の値」の組を返します。
 * partition と同じく添字 0 が true 側、添字 1 が false 側で、振り分け先は truthy 判定です。
 * 保存されるのは値ではなく個数と順序です。入力の各要素は変換後の値としてどちらか一方に 1 回だけ現れ、
 * 両側の要素数の合計は入力の要素数と一致します。どちらの側も入力の並び順を保ちます。
 *
 * 左右で異なる型の値を返しても構いませんが、左右の値は同じ型パラメータ E として扱われるため、
 * PHPStan 上は両側とも union として推論されます。
 *
 * @template K of array-key
 * @template V
 * @template E
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): array{0: bool, 1: E} $callback 振り分け先と変換後の値の組を返す関数（第1要素が true なら添字 0 側、false なら添字 1 側）
 * @return array{0: list<E>|array<K, E>, 1: list<E>|array<K, E>}
 * @phpstan-return ($mode is Mode::MODE_LIST ? array{0: list<E>, 1: list<E>} :
 *     ($mode is Mode::MODE_ASSOC ? array{0: array<K, E>, 1: array<K, E>} :
 *       ($input is list<V> ? array{0: list<E>, 1: list<E>} :
 *         array{0: array<K, E>, 1: array<K, E>}
 *  )))
 */
function partition_map(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [[], []];

    foreach ($input as $key => $value) {
        $classified = $callback($value, $key);
        $side       = $classified[0] ? 0 : 1;

        if ($mode === Mode::MODE_LIST) {
            $result[$side][] = $classified[1];
        } else {
            $result[$side][$key] = $classified[1];
        }
    }

    return $result;
}
