<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleCategory;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // print_r(VehicleModel::with(['brand', 'categories'])->get()->first()->id);die;
        return Inertia::render('Trip/Create', [
            'vehicle_brands' => VehicleBrand::all(),
            'vehicle_models' => VehicleModel::with(['brand', 'categories'])->get(),
            'vehicle_categories' => VehicleCategory::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {
        $departure_coord = $request->input('departure_coord');
        $arrival_coord = $request->input('departure_coord');
        $trip = Auth::user()->trips()->create([
            'departure_coord' => DB::raw("POINT(${departure_coord['lat']}, ${departure_coord['lon']})"),
            'arrival_coord' => DB::raw("POINT(${arrival_coord['lat']}, ${arrival_coord['lon']})"),
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($request->input('departure_datetime'))),
            'instant_booking' => $request->input('instant_booking'),
        ]);

        $trip->vehicleInfo()->create($request->input('vehicle_info'));
        echo "created:";    
        var_dump($trip);
        die;
        // $trip = Trip::create([
        //     'departure_coord' => DB::raw("POINT(${departure_coord['lat']}, ${departure_coord['lon']})"),
        //     'arrival_coord' => DB::raw("POINT(${arrival_coord['lat']}, ${arrival_coord['lon']})"),
        //     'departure_datetime' => date('Y-m-d H:i:s', strtotime($request->input('departure_datetime'))),
        //     'instant_booking' => $request->input('instant_booking'),
        //     'driver_id' => Auth::id()
        // ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        //
    }
}
