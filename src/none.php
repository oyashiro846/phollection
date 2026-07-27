<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 条件を満たす要素が一つもないかどうかを調べる (空配列は vacuous truth により true を返す)
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback フィルターする条件
 */
function none(array $input, callable $callback): bool
{
    return !any($input, $callback);
}
