<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

/**
 * 入力と同じ器の配列を返す操作を、適用対象の配列だけ保留した状態で表します。
 *
 * @internal 各関数の戻り値をそのまま呼び出してください。
 *
 * @template K of array-key           入力のキー型
 * @template V                        入力の値型
 * @template I of array-key|Preserve  出力のキー型。Preserve なら入力のキーを保存する
 * @template E                        出力の値型。Preserve なら入力の値型を保存する
 * @template TMode of Mode
 */
final readonly class ShapeOp
{
    /**
     * @param \Closure(list<V>|array<K, V>, Mode): (list<mixed>|array<array-key, mixed>) $apply 解決済み mode を受けて実処理を行う
     * @param TMode $mode
     */
    public function __construct(
        private \Closure $apply,
        private Mode $mode,
    ) {
    }

    /**
     * @template TK of K
     * @template TV of V
     *
     * @param list<TV>|array<TK, TV> $input 対象の配列
     * @return list<mixed>|array<array-key, mixed>
     * @phpstan-return (I is Preserve
     *     ? (TMode is Mode::MODE_LIST
     *         ? list<(E is Preserve ? TV : E)>
     *         : (TMode is Mode::MODE_ASSOC
     *             ? array<TK, (E is Preserve ? TV : E)>
     *             : ($input is list<TV>
     *                 ? list<(E is Preserve ? TV : E)>
     *                 : array<TK, (E is Preserve ? TV : E)>)))
     *     : (TMode is Mode::MODE_LIST
     *         ? list<(E is Preserve ? TV : E)>
     *         : (TMode is Mode::MODE_ASSOC
     *             ? array<I, (E is Preserve ? TV : E)>
     *             : ($input is list<TV>
     *                 ? list<(E is Preserve ? TV : E)>
     *                 : array<I, (E is Preserve ? TV : E)>))))
     */
    public function __invoke(array $input): array
    {
        // TK / I は __invoke 側のテンプレートとクラス側のテンプレートに分かれており、
        // PHPStan は array<TK, TV> と array<K, V> を繋げないため再アサーションする。
        // I は array-key|Preserve なのでキーに置くときは array-key と交差させる。
        /** @var \Closure(list<TV>|array<TK, TV>, Mode): (list<mixed>|array<TK, mixed>|array<I&array-key, mixed>) $apply */
        $apply = $this->apply;

        return $apply($input, Mode::check_mode($this->mode, $input));
    }
}
