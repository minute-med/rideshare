<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use App\Enums\PassengerStatusEnum;

class PassengerStatus implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return PassengerStatusEnum::from($value)->label();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        foreach(PassengerStatusEnum::cases() as $case) {
            if($case->label() === $value) {
                return $case->value;
            }
        }
        return $value;
    }
}
