<?php

namespace App\Models;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly bool $isActive,
        public readonly string $name,
        public readonly string $email,
        public readonly int $pointsBalance
    )
    {
    }

}