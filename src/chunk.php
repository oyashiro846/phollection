<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列を固定サイズ単位で分割します。末尾は $size に満たない端数のチャンクになります。
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param int $size 1 つのチャンクに含める要素数（1 以上）
 * @param Mode $mode MODE_LIST のときは各チャンク内を 0 始まりの連番にし、MODE_ASSOC のときは元のキーを保持する。MODE_AUTO のときは入力に応じて判定する
 * @throws \InvalidArgumentException $size が 1 未満のとき
 * @return list<list<V>|array<K, V>>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<list<V>> :
 *     ($mode is Mode::MODE_ASSOC ? list<array<K, V>> :
 *       ($input is list<V> ? list<list<V>> :
 *         list<array<K, V>>
 *  )))
 */
function chunk(array $input, int $size, Mode $mode = Mode::MODE_AUTO): array
{
    if ($size <= 0) {
        throw new \InvalidArgumentException('$size は 1 以上である必要があります。');
    }

    $mode = Mode::check_mode($mode, $input);

    return array_chunk($input, $size, $mode === Mode::MODE_ASSOC);
}
