<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列を条件でフィルタする操作を返します。
 *
 * 戻り値は $input を受け取る callable です。そのまま呼び出すか、PHP 8.5 のパイプ演算子の
 * 右辺に置いてください。
 *
 * ```
 * $adults = $users |> filter(fn (User $u): bool => $u->age >= 20);
 * ```
 *
 * @template K of array-key
 * @template V
 * @template TMode of Mode
 *
 * @param callable(V, K): bool $callback フィルターする条件
 * @param TMode $mode
 * @return callable(array<K, V>): (list<V>|array<K, V>)
 * @phpstan-return ShapeOp<K, V, Preserve, Preserve, TMode>
 */
function filter(callable $callback, Mode $mode = Mode::MODE_AUTO): callable
{
    /** @var ShapeOp<K, V, Preserve, Preserve, TMode> $op */
    $op = new ShapeOp(
        static function (array $input, Mode $resolved) use ($callback): array {
            // $value は TV ($input 由来) だが PHPStan は callable の引数を不変扱いするため
            // 証明できない。キー側も TK なので array-key に緩める。
            /** @var callable(mixed, array-key): bool $cb */
            $cb = $callback;

            if ($resolved === Mode::MODE_ASSOC) {
                return array_filter($input, $cb, ARRAY_FILTER_USE_BOTH);
            }

            $result = [];

            foreach ($input as $key => $value) {
                if ($cb($value, $key)) {
                    $result[] = $value;
                }
            }

            return $result;
        },
        $mode,
    );

    return $op;
}
