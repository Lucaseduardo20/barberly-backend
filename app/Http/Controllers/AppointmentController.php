<?php

namespace App\Http\Controllers;

use App\Data\AppointmentRequestData;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Data\AppointmentData;
use Ramsey\Collection\Collection;
use Spatie\LaravelData\DataCollection;

class AppointmentController extends Controller
{

    public function __construct(private readonly AppointmentService $service)
    {
    }

    public function index(Request $request)
    {
        $appointments = $request->user()->appointments()->with('customer')->get();

        return AppointmentData::collect(
            $appointments->map(fn($appointment) => AppointmentData::fromAppointment($appointment))
        );
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

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->all();

        $this
            ->service
            ->cancel($data['id'], $data['reason']);

        return response()->json(null, 204);
    }

    public function done(Request $request)
    {
        $data = $request->all();

        $this
            ->service
            ->done($data['id'], $data['payment_method']);

        return response()->json(['message' => 'Agendamento concluído com sucesso!'], 200);
    }
}

