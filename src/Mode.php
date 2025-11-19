<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

enum Mode
{
    /**
     * @template K of array-key
     * @template V
     * @param list<V>|array<K, V> $input
     * @return Mode::MODE_LIST|Mode::MODE_ASSOC
     */
    public static function check_mode(Mode $mode, array $input): Mode
    {
        if ($mode === Mode::MODE_AUTO) {
            return array_is_list($input) ? Mode::MODE_LIST : Mode::MODE_ASSOC;
        }

        return $mode;
    }

    case MODE_ASSOC;
    case MODE_LIST;
    case MODE_AUTO;
}
