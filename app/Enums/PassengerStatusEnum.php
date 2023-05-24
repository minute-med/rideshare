<?php

namespace App\Enums;

enum PassengerStatusEnum: int
{
    case Pending = 1;
    case Approved = 2;
    case CanceledByDriver = 3;
    case CanceledByUser = 4;

    public function label(): string
    {
        return match($this) {
            static::Pending => 'Pending',
            static::Approved => 'Approved',
            static::CanceledByDriver => 'Canceled by driver',
            static::CanceledByUser => 'Canceled by user',
        };
    }

}