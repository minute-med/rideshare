<?php

return [
    'protocol' => env('NOMINATIM_PROTOCOL', 'http'),
    'host' => env('NOMINATIM_HOST', 'nominatim'),
    'port' => config('NOMINATIM_PORT', 8080)
];