# 20. 品質ゲート（最低限）

次の全てを必ず満たす
- 既存テストが通る
- 変更箇所のテストを追加する
- テスト追加が難しい場合、理由と代替確認手段を書く

推奨
- 1PR = 1目的（広いなら分割）
- リファクタは lint/test 等の「外部検証器」を根拠にする

## push前の必須手順（自動修正込み）
push/commit 前に必ずCIでLinter/Formatter/Testを実行し、ツールが修正した差分も含めて提出する。

- `vendor/bin/php-cs-fixer fix`
- `vendor/bin/phpstan analyse -c phpstan.neon`
- `vendor/bin/phpunit tests`
