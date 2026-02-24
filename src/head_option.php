<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列の最初の要素を取得します。空配列の場合は null を返します。
 *
 * 注意: 最初の要素が null の場合と空配列の場合を区別できません。
 * 区別が必要な場合は empty($input) を事前にチェックしてください。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @return V|null 最初の要素、または配列が空の場合は null
 */
function head_option(array $input): mixed
{
    if (empty($input)) {
        return null;
    }

    $firstKey = array_key_first($input);

    return $input[$firstKey];
}
