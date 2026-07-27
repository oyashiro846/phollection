<?php

declare(strict_types=1);

/*
 * src/ 配下の関数定義ファイルをここでまとめて読み込みます。
 *
 * composer.json の autoload.files に 1 ファイルずつ列挙すると、関数を追加する PR が
 * すべて同じ行 (配列の末尾) で衝突します。読み込みをこのファイルへ集約し、
 * 並び順をファイル名の昇順に固定することで、追加位置が分散して衝突しにくくなります。
 *
 * 新しい関数を追加するときは、composer.json ではなくこのファイルへ
 * **昇順の正しい位置に** 1 行足してください。クラスは PSR-4 で解決されます。
 */

require_once __DIR__ . '/any.php';
require_once __DIR__ . '/chunk_by.php';
require_once __DIR__ . '/collect.php';
require_once __DIR__ . '/filter.php';
require_once __DIR__ . '/group_by.php';
require_once __DIR__ . '/head_option.php';
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/intersect.php';
require_once __DIR__ . '/last_option.php';
require_once __DIR__ . '/map.php';
require_once __DIR__ . '/map_keys.php';
require_once __DIR__ . '/reduce.php';
require_once __DIR__ . '/slice.php';
require_once __DIR__ . '/tail.php';
require_once __DIR__ . '/unique.php';
