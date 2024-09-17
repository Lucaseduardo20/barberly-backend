<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return Appointment::with('user', 'service')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_time' => 'required|date',
        ]);

        $appointment = new Appointment();
        $appointment->user_id = $request->user_id;
        $appointment->service_id = $request->service_id;
        $appointment->appointment_date = $request->appointment_time;
        $appointment->save();
        return response()->json($appointment, 201);
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

