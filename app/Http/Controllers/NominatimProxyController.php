<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NominatimProxyController extends Controller
{
    function search(Request $r)
    {
        $protocol = config('nominatim.protocol');
        $host = config('nominatim.host');
        $port = config('nominatim.port');
        $url = "${protocol}://${host}:${port}/search?q=" . $r->query('q') . '&accept-language=' . $r->query('accept-language');
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        $res = json_decode($response->getBody());
        return response()->json($res);
    }
}
