<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

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
function unique(
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
            $hash = _unique_get_strict_hash($value);

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
 * @internal unique() の内部ヘルパー関数
 */
function _unique_get_strict_hash(mixed $input): string
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
            $parts[] = $k . '=>' . _unique_get_strict_hash($v);
        }
        $hash = 'a:[' . implode(';', $parts) . ']';
    }

    return $hash;
}
