# Issue #47 API差分確定・P1/P2/P3仕様凍結

- Status: Frozen
- Frozen at: 2026-03-03
- Related: Issue #46 (feat: 不足した関数のタスク化)
- Scope: 仕様定義のみ（実装は本Issueでは行わない）

## 1. 目的
既存 API と Scala / Java Stream / C# LINQ / Laravel Collection / Kotlin / Clojure の主要 API を突き合わせ、追加対象と優先度（P1/P2/P3）を凍結する。合わせて命名衝突ポリシーと `Mode::MODE_AUTO` / キー保持原則を確定する。

## 2. 現行APIベースライン
現行（2026-03-03）で公開済みの関数:

- `map`
- `map_keys`
- `filter`
- `collect`
- `any`
- `reduce`
- `intersect`
- `unique`
- `slice`
- `init`
- `tail`
- `head_option`
- `last_option`

補足:

- 命名は snake_case を採用。
- コールバック引数は原則 `(value, key)`（`map_keys` のみ `(key, value)`）。
- `Mode::MODE_AUTO` により list/assoc を自動判定。

## 3. 差分サマリ（主要言語API対比）
本ライブラリで不足しているが、複数言語で共通して重要度が高い機能を追加対象とする。

- 変換: `flat_map`
- 分類: `group_by`, `partition`, `count_by`
- 探索: `find_option`, `all`, `none`
- 範囲抽出: `take`, `drop`, `take_while`, `drop_while`
- 結合: `zip`, `zip_with`
- バッチ/窓: `chunk`, `windowed`
- 並び替え: `sort_by`
- 連想変換: `associate`

### 3.1 対比マトリクス（凍結根拠）

| Phollection候補 | Scala | Java Stream | C# LINQ | Laravel Collection | Kotlin | Clojure |
| --- | --- | --- | --- | --- | --- | --- |
| `flat_map` | `flatMap` | `flatMap` | `SelectMany` | `flatMap` | `flatMap` | `mapcat` |
| `group_by` | `groupBy` | `Collectors.groupingBy` | `GroupBy` | `groupBy` | `groupBy` | `group-by` |
| `partition` | `partition` | `Collectors.partitioningBy` | (Where/Except相当) | `partition` | `partition` | `split-with`/`filter` |
| `find_option` | `find`(Option) | `findFirst`(Optional) | `FirstOrDefault` | `first` | `find` | `some` |
| `all` / `none` | `forall` / `none` | `allMatch` / `noneMatch` | `All` / `!Any` | `every` / `doesntContain`相当 | `all` / `none` | `every?` / `not-any?` |
| `count_by` | `groupMapReduce`相当 | `groupingBy + counting` | `GroupBy(...).Count()` | `countBy` | `groupingBy().eachCount()` | `frequencies` |
| `take` / `drop` | `take` / `drop` | `limit` / `skip` | `Take` / `Skip` | `take` / `skip` | `take` / `drop` | `take` / `drop` |
| `zip` / `zip_with` | `zip` | (標準なし) | `Zip` | `zip` | `zip` | `map vector`相当 |
| `chunk` / `windowed` | `grouped` / `sliding` | (標準なし) | `Chunk` / (windowは標準なし) | `chunk` / `sliding` | `chunked` / `windowed` | `partition` |
| `sort_by` | `sortBy` | `sorted(comparing)` | `OrderBy` | `sortBy` | `sortedBy` | `sort-by` |
| `associate` | `toMap`相当 | `toMap` | `ToDictionary` | `mapWithKeys` | `associate` | `into {}`相当 |

## 4. 命名衝突ポリシー（PHP組み込みとの整理）

### 4.1 原則
- グローバル関数衝突・曖昧性がある名称は避ける。
- 既存命名規約（snake_case）を維持する。
- 「戻り値の意味」が分かる名前を優先する（例: `find_option`）。

### 4.2 凍結した命名
- 採用: `find_option`（`find` は不採用）
- 採用: `sort_by`（`sort` は不採用）
- 採用: `count_by`（`count` は不採用）
- 採用: `chunk`（`array_chunk` と役割は近いが名前衝突はない）
- 採用: `windowed`（`sliding` は別名候補として将来検討）
- 採用: `associate`（`to_map` は不採用）

## 5. `Mode::MODE_AUTO` とキー保持の共通原則（凍結）

### 5.1 `Mode::MODE_AUTO`
- すべての Mode 対応関数は処理開始時に `Mode::check_mode($mode, $input)` を一度だけ評価する。
- AUTO 時は `array_is_list($input)` が `true` なら LIST、`false` なら ASSOC。
- 関数途中で入力構造が変わっても mode は再判定しない。

### 5.2 キー保持
- `Mode::MODE_ASSOC`: 可能な限り入力キーを保持する。
- `Mode::MODE_LIST`: 常に 0 始まり連番に正規化する。
- grouping / chunking / windowing / zip のような「新しい構造を構築する操作」は、外側コンテナは list を基本とし、内側のキー保持可否を関数ごとに明示する。

## 6. P1/P2/P3 凍結スコープ

## P1（最優先）

### `flat_map`
- 目的: 1要素 -> 複数要素（0件含む）への展開。
- シグネチャ案: `flat_map(array $input, callable $callback, Mode $mode = Mode::MODE_AUTO): array`
- コールバック: `(value, key) => array`
- 受け入れ条件:
  - callback の戻り配列を順序維持で平坦化する。
  - LIST モードでは結果は list。
  - ASSOC モードでは「外側キー」は保持せず、callback が返した配列キーを採用する。
  - 同一キー衝突時は後勝ち。

### `group_by`
- 目的: 要素を分類キーごとにグループ化。
- シグネチャ案: `group_by(array $input, callable $classifier): array`
- コールバック: `(value, key) => array-key`
- 受け入れ条件:
  - 戻り値は `array<array-key, array>`。
  - 同一グループ内の順序は入力順を保持。
  - 入力が ASSOC の場合、グループ内要素は入力キーを保持。

### `partition`
- 目的: 真偽条件で2分割。
- シグネチャ案: `partition(array $input, callable $predicate, Mode $mode = Mode::MODE_AUTO): array`
- 受け入れ条件:
  - 戻り値は `array{0: array, 1: array}`（0: true 側, 1: false 側）に固定。
  - 2配列の連結（順序考慮）で元入力の要素集合と等価。
  - 各配列は mode に従ってキー保持/連番化。

### `find_option`
- 目的: 条件一致する先頭要素を Option 表現で返す。
- シグネチャ案: `find_option(array $input, callable $predicate): ?array`
- 受け入れ条件:
  - 見つかった場合は `array{0: key, 1: value}`。
  - 未検出時は `null`。
  - 先頭一致で短絡評価する。

### `all`
- 目的: 全件が条件を満たすか判定。
- シグネチャ案: `all(array $input, callable $predicate): bool`
- 受け入れ条件:
  - 1件でも false なら短絡で false。
  - 空配列は true（vacuous truth）。

### `none`
- 目的: 条件を満たす要素が1件もないか判定。
- シグネチャ案: `none(array $input, callable $predicate): bool`
- 受け入れ条件:
  - 1件でも true があれば短絡で false。
  - 空配列は true。

### `count_by`
- 目的: 分類キーごとの件数集計。
- シグネチャ案: `count_by(array $input, callable $classifier): array`
- 受け入れ条件:
  - 戻り値は `array<array-key, int>`。
  - 未出現キーは含めない。
  - カウントは 1 以上の整数。

## P2（中優先）

### `take` / `drop`
- 受け入れ条件:
  - `take(n)`: 先頭から最大 n 件。
  - `drop(n)`: 先頭から n 件を除外。
  - `n <= 0` の扱いを明確化: `take` は空、`drop` は全件。
  - mode に従ってキー保持/連番化。

### `take_while` / `drop_while`
- 受け入れ条件:
  - 条件評価は先頭から順次、初回不一致で境界確定。
  - `take_while`: 境界手前まで。
  - `drop_while`: 境界以降。
  - mode に従ってキー保持/連番化。

### `zip`
- シグネチャ案: `zip(array $left, array $right): array`
- 受け入れ条件:
  - 戻り値は `list<array{0:mixed,1:mixed}>`。
  - 長さは `min(count($left), count($right))`。
  - キーは無視し、反復順序のみを使用。

### `zip_with`
- シグネチャ案: `zip_with(array $left, array $right, callable $callback): array`
- 受け入れ条件:
  - `zip` と同じ長さルール。
  - 各ペアに callback を適用した list を返す。

### `chunk`
- シグネチャ案: `chunk(array $input, int $size, Mode $mode = Mode::MODE_AUTO): array`
- 受け入れ条件:
  - 戻り値は `list<array>`。
  - `size <= 0` は `InvalidArgumentException`。
  - 各チャンク内部は mode に従ってキー保持/連番化。

### `windowed`
- シグネチャ案: `windowed(array $input, int $size, int $step = 1, bool $partial = false, Mode $mode = Mode::MODE_AUTO): array`
- 受け入れ条件:
  - 戻り値は `list<array>`。
  - `size <= 0` または `step <= 0` は `InvalidArgumentException`。
  - `partial=false` では完全窓のみ、`partial=true` では末尾不完全窓も返す。
  - 窓内部は mode に従ってキー保持/連番化。

## P3（後続）

### `sort_by`
- シグネチャ案: `sort_by(array $input, callable $selector, Mode $mode = Mode::MODE_AUTO): array`
- 受け入れ条件:
  - selector の比較キーで昇順ソート。
  - ASSOC モード時はキー保持。
  - LIST モード時は連番再付与。
  - 同値時の順序安定性は「安定ソートを目標、未保証の場合は仕様に明記」。

### `associate`
- シグネチャ案: `associate(array $input, callable $transform): array`
- コールバック: `(value, key) => array{0: array-key, 1: mixed}`
- 受け入れ条件:
  - 返却ペアの key/value から新しい連想配列を構築。
  - 同一キー衝突時は後勝ち。
  - 入力モードに依存せず戻り値は assoc。

## 7. スコープ外（本凍結に含めない）
- 例外メッセージ文言の厳密化。
- ジェネリクス注釈（phpstan-return）最適化。
- メソッドチェーン API / コレクションクラス導入。
- lazy / iterator ベース API。

## 8. 実装タスク分割ルール（Issue化前提）
- 1関数1Issueを基本に、`take/drop` と `take_while/drop_while` はペア実装を許可。
- 各 Issue に以下を必須化:
  - シグネチャ
  - mode/key保持ルール
  - エッジケース（空配列、負数、衝突）
  - テスト観点（正常/境界/異常）

## 9. 凍結結論
Issue #47 の結論として、上記 P1/P2/P3 と受け入れ条件を正式スコープとして凍結する。以後の変更は Issue #46 配下の個別 Issue で「仕様変更」として明示提案すること。
