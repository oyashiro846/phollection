<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * いずれかの要素がコールバック関数を満たすかどうかを調べる
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param callable(V, K): bool $callback フィルターする条件
 */
function any(array $input, callable $callback): bool
{
    return array_any($input, $callback);
}
