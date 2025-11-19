<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class CollectTest extends TestCase
{
    public function test_collect_list_auto_mode(): void
    {
        $input = [1, 2, 3, 4];

        $result = Arrays::collect(
            $input,
            fn (int $v): ?int => $v % 2 === 0 ? $v * 10 : null,
        );

        $this->assertSame([20, 40], $result);
    }

    public function test_collect_list_key_aware(): void
    {
        $input = [10, 20, 30];

        $result = Arrays::collect(
            $input,
            fn (int $v, int $i): ?int => $i === 1 ? $v + 1 : null,
            Mode::MODE_LIST,
        );

        $this->assertSame([21], $result);
    }

    public function test_collect_assoc_auto_mode(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
            'carol' => 23,
        ];

        $result = Arrays::collect(
            $input,
            function (int $age, string $name): ?array {
                if ($age < 20) {
                    return null;
                }

                return [
                    'name' => $name,
                    'age'  => $age,
                ];
            },
        );

        $this->assertSame([
            'alice' => ['name' => 'alice', 'age' => 20],
            'carol' => ['name' => 'carol', 'age' => 23],
        ], $result);
    }
}
