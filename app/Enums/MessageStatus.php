<?php

namespace App\Enums;

enum MessageStatus: int
{
    case received = 1;

    public function label(): string
    {
        return match($this) {
            static::received => 'Received',
        };
    }

}