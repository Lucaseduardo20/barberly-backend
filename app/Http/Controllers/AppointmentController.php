<?php

namespace App\Http\Controllers;

use App\Data\AppointmentRequestData;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->appointments()->get();
    }

    public function store(Request $request)
    {
        $validated = AppointmentRequestData::from($request->all());

        $appointment = new Appointment();
        $response = $appointment->schedule(collect($validated->all()));
        return response()->json($response, 201);
    }

    public function show($id)
    {
        return Appointment::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_time' => 'required|date',
        ]);
        $appointment = Appointment::findOrFail($id);
        $appointment->update($validated);
        return response()->json($appointment);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return response()->json(null, 204);
    }
}

