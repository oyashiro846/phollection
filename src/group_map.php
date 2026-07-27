<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * コールバックで指定した分類キーごとに配列をグループ化し、各要素を変換して格納します。
 *
 * group_by() の各要素を $transform で変換した版です。
 *
 * 各グループの中でのキーの扱いは $mode で決まります。
 * MODE_LIST では入力のキーを捨て、各グループを 0 始まり連番の list にします。
 * MODE_ASSOC では入力のキーを保持し、入力が list であっても元の整数添字をそのまま使います
 * （グループごとに要素が飛び飛びになっても添字を詰めません）。
 * MODE_AUTO は入力の形状から判定するため、group_by() と同じ挙動になります。
 *
 * グループの出現順、およびグループ内の要素の順序は入力順のままです。
 *
 * @template K of array-key
 * @template V
 * @template G of array-key
 * @template E
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): G $classifier 分類キーを返す関数（第1引数: 値, 第2引数: キー）
 * @param callable(V, K): E $transform 各要素を変換する関数（第1引数: 値, 第2引数: キー）
 * @return array<G, list<E>|array<K, E>>
 * @phpstan-return ($mode is Mode::MODE_LIST ? array<G, list<E>> :
 *    ($mode is Mode::MODE_ASSOC ? array<G, array<K, E>> :
 *      ($input is list<V> ? array<G, list<E>> :
 *        array<G, array<K, E>>
 * )))
 */
function group_map(array $input, callable $classifier, callable $transform, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $input);

    $result = [];

    foreach ($input as $key => $value) {
        $groupKey = $classifier($value, $key);
        $element  = $transform($value, $key);

        $result[$groupKey] ??= [];

        if ($mode === Mode::MODE_LIST) {
            $result[$groupKey][] = $element;
        } else {
            $result[$groupKey][$key] = $element;
        }
    }

    return $result;
}
