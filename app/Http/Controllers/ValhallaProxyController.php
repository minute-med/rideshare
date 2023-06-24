<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValhallaProxyController extends Controller
{
    function route(Request $r) {
        $protocol = config('valhalla.protocol');
        $host = config('valhalla.host');
        $port = config('valhalla.port');
        $url = "{$protocol}://{$host}:{$port}/route";
        $client = new \GuzzleHttp\Client();

        $response = $client->post($url, [
            \GuzzleHttp\RequestOptions::JSON => $r->all()
        ]);
        $res = json_decode($response->getBody());
        return response()->json($res);
    }
}
