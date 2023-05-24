<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SQLPoint implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return unpack('x/x/x/x/corder/Ltype/dlon/dlat', $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return DB::raw("ST_SRID(POINT({$value['lon']}, {$value['lat']}), 4326)");
        // return pack('xxxxcLdd', '0', 1, $value['lat'], $value['lon']);
    }
}
