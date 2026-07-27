<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 左右の配列を反復順序で組み合わせ、ペアの配列を作ります。
 * 右のキーは一切使用せず、反復順序のみでペアにします。
 * MODE_ASSOC では左のキーを維持し、MODE_LIST では左右のキーを無視して list を返します。
 *
 * @template K of array-key
 * @template L
 * @template R
 *
 * @param list<L>|array<K, L> $left 左側の配列
 * @param array<array-key, R> $right 右側の配列
 * @return list<array{0: L, 1: R}>|array<K, array{0: L, 1: R}> 長さは min(count($left), count($right))
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<array{0: L, 1: R}> :
 *   ($mode is Mode::MODE_ASSOC ? array<K, array{0: L, 1: R}> :
 *     ($left is list<L> ? list<array{0: L, 1: R}> :
 *       array<K, array{0: L, 1: R}>
 * )))
 */
function zip(array $left, array $right, Mode $mode = Mode::MODE_AUTO): array
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
            $result[] = [$value, $rightValues[$index]];
        } else {
            $result[$key] = [$value, $rightValues[$index]];
        }

        $index++;
    }

    return $result;
}
