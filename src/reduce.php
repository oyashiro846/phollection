<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列を畳み込んで単一の値にします.
 *
 * @template V
 * @template R
 *
 * @param array<V> $input
 * @param callable(R|V|null, V): (R|V|null) $callback
 * @param R $initial
 * @phpstan-param (R is null ?
 *    callable(?V, V): ?V :
 *    callable(R, V): R
 *  ) $callback
 * @return R|V|null
 * @phpstan-return (R is null ? (?V) : R)
 */
function reduce(array $input, callable $callback, mixed $initial = null): mixed
{
    // $initial が null の場合、 R が登場しないので型パラメータは論理的に整合するが、
    // PHPStan は関数内のチェックでは分岐をしてくれず、 callback(R|V, V): R|V を要求してしまう
    // @phpstan-ignore argument.type
    return array_reduce($input, $callback, $initial);
}
