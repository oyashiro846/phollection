<?php

declare(strict_types=1);

namespace Oyashiro846\PhpCollection;

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
        $result = array_filter($input, $callback, ARRAY_FILTER_USE_BOTH);

        return Mode::check_mode($mode, $input) === Mode::MODE_LIST
            ? array_values($result) : $result;
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
}
