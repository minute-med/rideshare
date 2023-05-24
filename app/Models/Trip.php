<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\SQLPoint;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trip extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'departure_coord'           => SQLPoint::class,
        'arrival_coord'             => SQLPoint::class,
        'instant_booking'           => 'boolean',
        'departure_datetime'        => 'datetime:d/m/Y H:i A',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'departure_datetime',
        'instant_booking',
        'departure_addr',
        'departure_coord',
        'arrival_addr',
        'arrival_coord',
        'driver_id',
        'price',
    ];

    function price(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value / 100,
            set: fn (string $value) => $value * 100,
        );
    }

    public function driver ()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicleInfo()
    {
        return $this->hasOne(VehicleInfo::class);
    }

    public function passengers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_passenger', 'trip_id', 'passenger_id')
        ->using(TripPassenger::class)
        ->withPivot('status');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
