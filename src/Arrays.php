<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 *  array を連想配列として扱うか、リストとして扱うかを区別するため、各関数では以下のオプションが指定できます。
 *  - $mode が {@see Mode::MODE_LIST} の場合は list として扱い、結果も添字を詰めた list で返します。
 *  - $mode が {@see Mode::MODE_ASSOC} の場合は連想配列として扱い、キーを保持したまま返します。
 *  - $mode が {@see Mode::MODE_AUTO} の場合は渡された配列から適切なモードを判定します。
 */
class Arrays
{
    /**
     * 配列を条件でフィルタします。
     *
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @param callable(V, K): bool $callback フィルターする条件
     * @phpstan-param ($mode is Mode::MODE_LIST ? list<V> :
     *   ($mode is Mode::MODE_ASSOC ? array<K, V> :
     *     list<V>|array<K, V>
     * )) $input
     * @return list<V>|array<K, V>
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
     *     ($mode is Mode::MODE_ASSOC ? array<K, V>:
     *       ($input is list<V> ? list<V> :
     *         array<K, V>
     *  )))
     */
    public static function filter(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
    {
        $mode = Mode::check_mode($mode, $input);

        $result = [];

        foreach ($input as $key => $value) {
            if ($callback($value, $key)) {
                if ($mode === Mode::MODE_LIST) {
                    $result[] = $value;
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * いずれかの要素がコールバック関数を満たすかどうかを調べる
     *
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @param callable(V, K): bool $callback フィルターする条件
     */
    public static function any(array $input, callable $callback): bool
    {
        return array_any($input, $callback);
    }

    /**
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @return list<V>|array<K, V>
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
     *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
     *       ($input is list<V> ? list<V> :
     *         array<K, V>
     *  )))
     */
    public static function tail(array $input, Mode $mode = Mode::MODE_AUTO): array
    {
        return \array_slice(
            $input,
            1,
            null,
            Mode::check_mode($mode, $input) === Mode::MODE_ASSOC,
        );
    }

    /**
     * 配列を変換しつつ、null になった要素を取り除きます。
     *
     * @template K of array-key
     * @template V
     * @template E
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @param callable(V, K): ?E $callback $callback フィルターする条件
     * @phpstan-param ($mode is Mode::MODE_LIST ? list<V> :
     *    ($mode is Mode::MODE_ASSOC ? array<K, V> :
     *      list<V>|array<K, V>
     *  )) $input
     * @return list<E>|array<K, E>
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<E> :
     *    ($mode is Mode::MODE_ASSOC ? array<K, E> :
     *      ($input is list<V> ? list<E> :
     *        array<K, E>
     * )))
     */
    public static function collect(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
    {
        $mode = Mode::check_mode($mode, $input);

        $result = [];

        foreach ($input as $key => $value) {
            $element = $callback($value, $key);

            if (!\is_null($element)) {
                if ($mode === Mode::MODE_LIST) {
                    $result[] = $element;
                } elseif ($mode === Mode::MODE_ASSOC) {
                    $result[$key] = $element;
                }
            }
        }

        return $result;
    }

    /**
     * 配列の一部を展開する
     *
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
     *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
     *       ($input is list<V> ? list<V> :
     *         array<K, V>
     *  )))
     */
    public static function slice(
        array $input,
        int $offset,
        ?int $length = null,
        Mode $mode = Mode::MODE_AUTO,
    ): array {
        $mode = Mode::check_mode($mode, $input);

        return \array_slice($input, $offset, $length, $mode === Mode::MODE_ASSOC);
    }

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
    public static function reduce(array $input, callable $callback, mixed $initial = null): mixed
    {
        // $initial が null の場合、 R が登場しないので型パラメータは論理的に整合するが、
        // PHPStan は関数内のチェックでは分岐をしてくれず、 callback(R|V, V): R|V を要求してしまう
        // @phpstan-ignore argument.type
        return array_reduce($input, $callback, $initial);
    }

    /**
     * @template K of array-key
     * @template V
     * @template E
     *
     * @param list<V>|array<K, V> $input
     * @param callable(V, K): E $callback
     *
     * @return list<E>|array<K, E>
     *
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<E> :
     *   ($mode is Mode::MODE_ASSOC ? array<K, E> :
     *     ($input is list<V> ? list<E> :
     *       array<K, E>
     *     )
     *   )
     * )
     */
    public static function map(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array
    {
        $mode = Mode::check_mode($mode, $input);

        $keys   = array_keys($input);
        $result = array_map($callback, $input, $keys);

        if ($mode === Mode::MODE_ASSOC) {
            return array_combine($keys, $result);
        }

        return $result;
    }

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
    public static function head_option(array $input): mixed
    {
        if (empty($input)) {
            return null;
        }

        $firstKey = array_key_first($input);

        return $input[$firstKey];
    }

    /**
     * 配列の最後の要素を取得します。空配列の場合は null を返します。
     *
     * 注意: 最後の要素が null の場合と空配列の場合を区別できません。
     * 区別が必要な場合は empty($input) を事前にチェックしてください。
     *
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @return V|null 最後の要素、または配列が空の場合は null
     */
    public static function last_option(array $input): mixed
    {
        if (empty($input)) {
            return null;
        }

        $lastKey = array_key_last($input);

        return $input[$lastKey];
    }
}
