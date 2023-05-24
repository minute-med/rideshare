<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleInfo extends Model
{
    use HasFactory;

        /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'max_seats',
        'model_id',
        'category_id',
        'color',
        'license_plate',
    ];

    public function model() {
        return $this->BelongsTo(VehicleModel::class, 'model_id');
    }

    public function category() {
        return $this->BelongsTo(VehicleCategory::class, 'category_id');
    }
}
