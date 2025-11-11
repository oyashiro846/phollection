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
}
