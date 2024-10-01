<?php

namespace App\ViewModels;

class UserViewModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly int $points_balance,
    )
    {
    }

}
