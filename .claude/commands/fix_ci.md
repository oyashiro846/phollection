# /project:fix_ci
目的：CI失敗を最小パッチで直す（安全寄り）。

## push前の必須手順（自動修正込み）
push/commit 前に必ず以下を実行し、ツールが修正した差分も含めて提出する。

- `vendor/bin/php-cs-fixer fix`
- `vendor/bin/phpstan analyse -c phpstan.neon`
- `vendor/bin/phpunit tests`

## 入力
- 失敗ログの要点
- 失敗したコマンド / ジョブ名

## ルール
- Stop対象は触らない
- 修正は最小パッチ
- 2回失敗したら止めて状況整理

## 出力
- 原因仮説（2〜3個）
- 最小修正案
- 再実行結果
