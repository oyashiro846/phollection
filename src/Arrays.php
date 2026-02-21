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

        if ($mode === Mode::MODE_ASSOC) {
            return array_filter($input, $callback, ARRAY_FILTER_USE_BOTH);
        }

        $result = [];

        foreach ($input as $key => $value) {
            if ($callback($value, $key)) {
                $result[] = $value;
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
     * 配列から重複した値を削除する
     *
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @param bool $strict true のときは型も区別する厳密な比較を行う（===相当）。false のときは array_unique に $flags を渡す
     * @param int $flags $strict=false のときの比較方法。デフォルトは SORT_REGULAR（array_unique の デフォルトは SORT_STRING とは異なる）。$strict=true の場合は $flags は無視される。
     * @param Mode $mode MODE_LIST のときは list として扱いインデックスを詰める。MODE_ASSOC のときはキーを保持。MODE_AUTO のときは入力に応じて判定
     *
     * 注意: array_uniqueのラッパーとして $flags を残していますが、$strict=true と $flags を同時に指定することは推奨されません。この場合、$flags は無視されます。
     *
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
     *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
     *       ($input is list<V> ? list<V> :
     *         array<K, V>
     * )))
     */
    public static function unique(
        array $input,
        bool $strict = false,
        int $flags = SORT_REGULAR,
        Mode $mode = Mode::MODE_AUTO,
    ): array {
        $mode = Mode::check_mode($mode, $input);

        if (!$strict) {
            $result = array_unique($input, $flags);

            if ($mode === Mode::MODE_LIST) {
                $result = array_values($result);
            }
        } else {
            $seen   = [];
            $result = [];

            foreach ($input as $key => $value) {
                $hash = self::get_strict_hash($value);

                if (!isset($seen[$hash])) {
                    $seen[$hash] = true;

                    if ($mode === Mode::MODE_LIST) {
                        $result[] = $value;
                    } else {
                        $result[$key] = $value;
                    }
                }
            }
        }

        return $result;
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

    /**
     * 2つの配列の共通要素を返します（積集合）。
     *
     * PHP の {@see array_intersect()} のラッパーです。
     * 第1配列に含まれる値のうち、第2配列にも存在するものだけを返します。
     * 比較は値の文字列表現で行われます（array_intersect の仕様）。
     *
     * @template K of array-key
     * @template V
     *
     * @param list<V>|array<K, V> $input 対象の配列
     * @param list<V>|array<array-key, V> $other 比較対象の配列
     * @return list<V>|array<K, V>
     * @phpstan-return ($mode is Mode::MODE_LIST ? list<V> :
     *     ($mode is Mode::MODE_ASSOC ? array<K, V> :
     *       ($input is list<V> ? list<V> :
     *         array<K, V>
     *  )))
     */
    public static function intersect(array $input, array $other, Mode $mode = Mode::MODE_AUTO): array
    {
        $mode = Mode::check_mode($mode, $input);

        if ($mode === Mode::MODE_LIST) {
            // LIST モードでは foreach で直接リストを構築（array_values の O(N) を回避）
            $result = [];

            foreach ($input as $value) {
                if (\in_array($value, $other, false)) {
                    $result[] = $value;
                }
            }

            return $result;
        }

        // ASSOC モードでは array_intersect を使用（キーを保持）
        return array_intersect($input, $other);
    }

    /**
     * 配列のキーを変換します。値は保持されます。
     *
     * 注意: 複数のキーが同じ値に変換された場合、後のエントリの値が前のエントリを上書きします。
     *
     * コールバックの引数順序について:
     * この関数は「キーの変換」が主目的のため、コールバックは (key, value) の順で受け取ります。
     * これは map() や filter() などの (value, key) 順とは異なります。
     *
     * ```php
     * // ✅ 正しい使い方: 第1引数がキー、第2引数が値
     * Arrays::map_keys($arr, fn(string $key, int $value): string => strtoupper($key));
     *
     * // ❌ 間違い: map() と同じ引数順序を期待している
     * Arrays::map_keys($arr, fn(int $value, string $key): string => strtoupper($key));
     * ```
     *
     * @template K of array-key
     * @template V
     * @template R of array-key
     *
     * @param array<K, V> $input 対象の配列
     * @param callable(K, V): R $callback キーを変換する関数（第1引数: キー, 第2引数: 値）
     * @return array<R, V> キーが変換された新しい配列
     */
    public static function map_keys(array $input, callable $callback): array
    {
        $result = [];

        foreach ($input as $key => $value) {
            $newKey          = $callback($key, $value);
            $result[$newKey] = $value;
        }

        return $result;
    }

    private static function get_strict_hash(mixed $input): string
    {
        $hash = '';

        if ($input === null) {
            $hash = 'null:null';
        } elseif (\is_scalar($input)) {
            $type = get_debug_type($input);
            $hash = $type . ':' . (string)$input;
        } elseif (\is_resource($input)) {
            $hash = 'r:' . (int)$input;
        } elseif (\is_object($input)) {
            $hash = 'o:' . spl_object_hash($input);
        } elseif (\is_array($input)) {
            $parts = [];

            foreach ($input as $k => $v) {
                $parts[] = $k . '=>' . self::get_strict_hash($v);
            }
            $hash = 'a:[' . implode(';', $parts) . ']';
        }

        return $hash;
    }
}
