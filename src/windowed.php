<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 配列上をスライディング窓で走査し、各窓を並べた list を返します。
 *
 * 窓の開始位置は 0, $step, 2 * $step, ... と進みます。
 * $partial が false のときは要素が $size 個そろう完全な窓だけを返し、
 * true のときは開始位置が要素数未満であれば残りが $size 未満でも窓を作ります（空の窓は作りません）。
 *
 * 例:
 *   windowed([1, 2, 3, 4, 5], 3)                  // [[1, 2, 3], [2, 3, 4], [3, 4, 5]]
 *   windowed([1, 2, 3, 4, 5], 3, 2)               // [[1, 2, 3], [3, 4, 5]]
 *   windowed([1, 2, 3, 4, 5], 3, 2, true)         // [[1, 2, 3], [3, 4, 5], [5]]
 *   windowed([1, 2], 3)                           // []
 *   windowed([1, 2], 3, 1, true)                  // [[1, 2], [2]] （開始位置 1 の窓も作るため 2 個返る）
 *
 * @template K of array-key
 * @template V
 *
 * @param list<V>|array<K, V> $input 対象の配列
 * @param int $size 1 つの窓に含める要素数（1 以上）
 * @param int $step 窓の開始位置を進める幅（1 以上）
 * @param bool $partial true のときは末尾の欠けた窓も返す
 * @param Mode $mode MODE_LIST のときは各窓内を 0 始まりの連番にし、MODE_ASSOC のときは元のキーを保持する。MODE_AUTO のときは入力に応じて判定する
 * @throws \InvalidArgumentException $size または $step が 1 未満のとき
 * @return list<list<V>|array<K, V>>
 * @phpstan-return ($mode is Mode::MODE_LIST ? list<list<V>> :
 *     ($mode is Mode::MODE_ASSOC ? list<array<K, V>> :
 *       ($input is list<V> ? list<list<V>> :
 *         list<array<K, V>>
 *  )))
 */
function windowed(
    array $input,
    int $size,
    int $step = 1,
    bool $partial = false,
    Mode $mode = Mode::MODE_AUTO,
): array {
    if ($size <= 0) {
        throw new \InvalidArgumentException('$size は 1 以上である必要があります。');
    }

    if ($step <= 0) {
        throw new \InvalidArgumentException('$step は 1 以上である必要があります。');
    }

    $mode = Mode::check_mode($mode, $input);

    $count  = \count($input);
    $result = [];

    for ($offset = 0; $offset < $count; $offset += $step) {
        if (!$partial && $offset + $size > $count) {
            break;
        }

        // キーを保持したまま切り出し、MODE_LIST のときだけ窓の中を連番へ振り直す
        // （array_slice は $preserve_keys が false でも文字列キーを維持するため）
        $window = \array_slice($input, $offset, $size, true);

        $result[] = $mode === Mode::MODE_ASSOC ? $window : array_values($window);
    }

    return $result;
}
