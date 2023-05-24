<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Casts\PassengerStatus;

class TripPassenger extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trip_passenger';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status'   => PassengerStatus::class
    ];


    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }
}
