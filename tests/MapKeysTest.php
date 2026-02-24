<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class MapKeysTest extends TestCase
{
    public function testMapKeysTransformsKeys(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];

        $result = map_keys(
            $input,
            fn (string $key, int $value): string => strtoupper($key),
        );

        $this->assertSame(
            ['A' => 1, 'B' => 2, 'C' => 3],
            $result,
        );
    }

    public function testMapKeysWithNumericKeys(): void
    {
        $input = [1 => 'one', 2 => 'two', 3 => 'three'];

        $result = map_keys(
            $input,
            fn (int $key, string $value): int => $key * 10,
        );

        $this->assertSame(
            [10 => 'one', 20 => 'two', 30 => 'three'],
            $result,
        );
    }

    public function testMapKeysWithKeyCollision(): void
    {
        $input = ['beer' => 2.7, 'bisquit' => 5.8];

        $result = map_keys(
            $input,
            fn (string $key, float $value): string => substr($key, 0, 1),
        );

        // Both keys start with 'b', so the latter value (5.8) should overwrite
        $this->assertSame(
            ['b' => 5.8],
            $result,
        );
    }

    public function testMapKeysPreservesValues(): void
    {
        $input = ['x' => 100, 'y' => 200];

        $result = map_keys(
            $input,
            fn (string $key, int $value): string => $key . '_new',
        );

        $this->assertSame(
            ['x_new' => 100, 'y_new' => 200],
            $result,
        );
    }

    public function testMapKeysOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = map_keys(
            $input,
            fn ($key, $value) => $key,
        );

        $this->assertSame([], $result);
    }

    public function testMapKeysWithMixedTypes(): void
    {
        $input = ['a' => 'alpha', 'b' => 'beta'];

        $result = map_keys(
            $input,
            fn (string $key, string $value): int => \strlen($value),
        );

        // Both values have length 5 and 4
        $this->assertSame(
            [5 => 'alpha', 4 => 'beta'],
            $result,
        );
    }
}
