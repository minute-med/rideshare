<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SearchTripRequest;
use Inertia\Inertia;
use App\Models\Trip;

class SearchController extends Controller
{
    public function index ()
    {
        return Inertia::render('Trip/Search');
    }

    public function search (SearchTripRequest $r)
    {
        $departure_coord = $r->input('departure_coord');
        $arrival_coord = $r->input('arrival_coord');
        $departure_datetime = date('Y-m-d H:i:s', strtotime($r->input('departure_datetime')));
        $requiredSeats = $r->input('seats');

        $guestDeparturePoint = "ST_SRID(POINT(" . $departure_coord['lon'].", ".$departure_coord['lat']."), 4326)";
        $guestArrivalPoint = "ST_SRID(POINT(". $arrival_coord['lon'].", ". $arrival_coord['lat']."), 4326)";

        $res = DB::table('trips')
        ->join('vehicle_infos', 'vehicle_infos.trip_id', '=', 'trips.id')
        ->leftJoin('trip_passenger', 'trips.id', '=', 'trip_passenger.trip_id')
        ->select(
            DB::raw('trips.id, trips.departure_datetime, trips.driver_id'),
            DB::raw('COUNT(trip_passenger.passenger_id) as used_seats'),
            DB::raw('vehicle_infos.max_seats'),
            DB::raw("ST_Distance_Sphere(". $guestDeparturePoint.", departure_coord)/1000 as departure_distance"),
            DB::raw("ST_Distance_Sphere(".$guestArrivalPoint.", arrival_coord)/1000 as arrival_distance")
        )
        ->groupByRaw('trips.id, vehicle_infos.max_seats, trips.departure_datetime, trips.driver_id')
        ->havingRaw('used_seats < vehicle_infos.max_seats')
        ->havingRaw('departure_distance < 10')
        // bug here when unauthenticated request
        ->havingRaw('trips.driver_id != ' . Auth::user()->id)
        ->havingRaw("DATEDIFF(trips.departure_datetime, '$departure_datetime') = 0")
        ->get();
        
        $res = $res->pluck('id')->toArray();
        $results = Trip::whereIn('id', $res)->with(['driver', 'vehicleInfo', 'passengers'])->get();

        return Inertia::render('Trip/Search', [
            'search_results' => $results->toArray(),
            'form_data' => (object) $r->all()
        ]);
    }
}
