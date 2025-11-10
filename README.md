# {pacakge-name}
<img src="https://poser.pugx.org/oyashiro846/{pacakge-name}/v/stable" alt="Latest Stable Version">
<img src="https://poser.pugx.org/oyashiro846/{pacakge-name}/downloads" alt="Total Downloads">
<img src="https://poser.pugx.org/oyashiro846/{pacakge-name}/license" alt="License">

## 概要 (Overview)
`{pacakge-name}` は、PHP の標準配列だけで Scala のようなコレクション操作（`map`, `filter`, `groupBy`, `partition` など）を扱えるようにする軽量ユーティリティです。
[Laravel](https://laravel.com/) の [illuminate/collections](https://github.com/illuminate/collections) のようなメソッドチェーンや独自クラスを導入せず、 array や null だけですべての戻り値を表現します。 
多くの操作については [Scala Visual Reference](https://superruzafa.github.io/visual-scala-reference/ja/) によって概念を理解できます。

## インストール (Installation)

```shell
composer require oyashiro846/{pacakge-name}
```

## 使い方 (Usage)

```php
<?php

$nums = [1, 2, 3, 4];

$result = Arrays::map($nums, fn (int $v) => $v * 2);

assert([2, 4, 6, 8], $result);
```

## 設計思想 (Design Philosophy)

### 標準配列のみ
戻り値はすべて PHP の `array` または `null` で表現し、 Option やタプルも新しいクラスを定義せずに `?`, `array{0: ..., 1: ...}` のような形で表現します。

### メソッドチェーンを使わない
オブジェクトを生成してチェーンしていくスタイルは採らず、静的メソッドや名前付き関数を呼び出すだけの薄いAPIにします。
これにより既存コードへの組み込みや将来の標準関数への置き換えが容易になります。

### 自動判定 + モード指定
デフォルトでは `array_is_list` の結果に従って list/assoc を自動判定します。
意味的に assoc として扱いたい場合などは `mode: Mode::ASSOC` のように明示して挙動を上書きできます。