<?php

declare(strict_types=1);

namespace Oyashiro846\Phollection;

use PHPUnit\Framework\TestCase;

final class AssociateTest extends TestCase
{
    public function testAssociateOnEmptyArrayReturnsEmpty(): void
    {
        $input = [];

        $result = associate(
            $input,
            fn ($value, $index): array => [$index, $value],
        );

        $this->assertSame([], $result);
    }

    public function testAssociateOnListInput(): void
    {
        $input = [1, 2, 3];

        $result = associate(
            $input,
            fn (int $value, int $index): array => ['key' . $value, $value * 10],
        );

        $this->assertSame([
            'key1' => 10,
            'key2' => 20,
            'key3' => 30,
        ], $result);
    }

    public function testAssociateOnAssocInputDiscardsOriginalKeys(): void
    {
        $input = [
            'alice' => 20,
            'bob'   => 17,
        ];

        $result = associate(
            $input,
            fn (int $age, string $name): array => [$name . '_key', $age],
        );

        $this->assertSame([
            'alice_key' => 20,
            'bob_key'   => 17,
        ], $result);
    }

    public function testAssociateOnKeyCollisionKeepsLastValue(): void
    {
        $input = [1, 2, 3];

        $result = associate(
            $input,
            fn (int $value): array => ['same', $value],
        );

        $this->assertSame([
            'same' => 3,
        ], $result);
    }

    public function testAssociateWithIntegerKeys(): void
    {
        $input = ['x', 'y', 'z'];

        $result = associate(
            $input,
            fn (string $value, int $index): array => [$index * 2, $value],
        );

        $this->assertSame([
            0 => 'x',
            2 => 'y',
            4 => 'z',
        ], $result);
    }

    public function testAssociateAllowsNullValue(): void
    {
        $input = ['a', 'b'];

        $result = associate(
            $input,
            fn (string $value): array => [$value, null],
        );

        $this->assertSame([
            'a' => null,
            'b' => null,
        ], $result);
    }

    public function testAssociateDoesNotMutateInput(): void
    {
        $input = [1, 2, 3];

        associate(
            $input,
            fn (int $value): array => ['key' . $value, $value],
        );

        $this->assertSame([1, 2, 3], $input);
    }
}
