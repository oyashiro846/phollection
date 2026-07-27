<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * zip と同じ組み合わせ規則で左右の配列からペアを作り、各ペアに $callback を適用した結果を返します。
 * 右のキーは一切使用せず、反復順序のみでペアにします。
 * MODE_ASSOC では左のキーを維持し、MODE_LIST では左右のキーを無視して list を返します。
 *
 * @template K of array-key
 * @template L
 * @template R
 * @template E
 *
 * @param list<L>|array<K, L> $left 左側の配列
 * @param array<array-key, R> $right 右側の配列
 * @param callable(L, R): E $callback ペアに適用する関数（第1引数: $left の値, 第2引数: $right の値）
 * @return list<E>|array<K, E> 長さは min(count($left), count($right))
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<E> :
 *   ($mode is Mode::MODE_ASSOC ? array<K, E> :
 *     ($left is list<L> ? list<E> :
 *       array<K, E>
 * )))
 */
function zip_with(array $left, array $right, callable $callback, Mode $mode = Mode::MODE_AUTO): array
{
    $mode = Mode::check_mode($mode, $left);

    $length = min(\count($left), \count($right));

    $rightValues = array_values($right);

    $result = [];
    $index  = 0;

    foreach ($left as $key => $value) {
        if ($index >= $length) {
            break;
        }

        if ($mode === Mode::MODE_LIST) {
            $result[] = $callback($value, $rightValues[$index]);
        } else {
            $result[$key] = $callback($value, $rightValues[$index]);
        }

        $index++;
    }

    return $result;
}
