<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'departure_datetime',
        'instant_booking',
        'departure_coord',
        'arrival_coord',
        'driver_id',
    ];

    public function driver() {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicleInfo() {
        return $this->hasOne(VehicleInfo::class);
    }

    public function passengers() {
        return $this->belongsToMany(User::class, 'trip_passenger');
    }
}
