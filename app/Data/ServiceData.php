<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ServiceData extends Data
{
    public function __construct(
        public int $price,
        public string $name
    )
    {}

    public static function fromData(array $data): array
    {
        return array_map(fn ($item) => new self(
            price: $item['price'],
            name: $item['name']
        ), $data);
    }
}
