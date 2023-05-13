<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleBrand;

class VehicleModel extends Model
{
    use HasFactory;

    public function brand ()
    {
        return $this->belongsTo(VehicleBrand::class, 'vehicle_brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(VehicleCategory::class, 'model_category');
    }
}
