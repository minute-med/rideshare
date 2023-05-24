<?php

namespace App\Http\Controllers;

use App\Enums\MessageStatus;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Trip;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Models\VehicleCategory;
use App\Enums\PassengerStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $arrival_coord = $request->input('arrival_coord');

        $trip = Auth::user()->trips()->create([
            'price' => $request->input('price'),
            'departure_addr' => $request->input('departure_addr'),
            'arrival_addr' => $request->input('arrival_addr'),
            'departure_coord' => [
                'lon' => $departure_coord['lon'],
                'lat' => $departure_coord['lat'],
            ],
            'arrival_coord' => [
                'lon' => $arrival_coord['lon'],
                'lat' => $arrival_coord['lat'],
            ],
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($request->input('departure_datetime'))),
            'instant_booking' => $request->input('instant_booking'),
        ]);
        $trip->vehicleInfo()->create($request->input('vehicle_info'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        return Inertia::render('Trip/Show', [
            'trip' => $trip->load('driver', 'vehicleInfo.model.brand','vehicleInfo.category', 'passengers'),
        ]);
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

    public function book (Request $r, Trip $trip)
    {
        $status = $trip->instant_booking ? PassengerStatusEnum::Approved : PassengerStatusEnum::Pending;
        $trip->passengers()->attach(Auth::user()->id, [
            'status'    => $status,
            'seats'     => $r->input('seats'), // vulnerable if > 128
        ]);

        return Inertia::render('Trip/Show', [
            'trip' => $trip->load('driver', 'vehicleInfo.model.brand','vehicleInfo.category', 'passengers'),
        ]);
    }

    public function approve(Request $r, Trip $trip)
    {
        $trip->passengers()->updateExistingPivot($r->input('passenger_id'), ['status' => PassengerStatusEnum::Approved]);
        return to_route('profile.trips');
    }

    public function sendMessage(StoreMessageRequest $r, Trip $trip) {

        $message = $trip->messages()->create([
            'user_id' => Auth::user()->id,
            'content' => $r->input('content'),
            'status' => MessageStatus::received,
        ]);

        $data = [
            'user' => Auth::user(),
            'content' => $r->input('content'),
        ];
        $done = \Illuminate\Support\Facades\Redis::publish('message', json_encode($data));
    }
}
