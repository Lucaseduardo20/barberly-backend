<?php

namespace App\Http\Controllers;

use App\Data\AppointmentRequestData;
use App\Models\Appointment;
use App\Models\User;
use App\Services\AppointmentService;
use App\Data\AppointmentData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{

    public function __construct(private readonly AppointmentService $service)
    {
    }

    public function index(Request $request)
    {
        $appointments = $this->appointmentsFor($request)
            ->with(['customer', 'services', 'user'])
            ->get();

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

    public function show(Request $request, $id)
    {
        return $this->appointmentsFor($request)
            ->with(['customer', 'services', 'user'])
            ->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => ['sometimes', 'exists:users,id'],
            'appointment_date' => ['sometimes', 'date'],
            'appointment_time' => ['sometimes', 'date_format:H:i'],
            'status' => ['sometimes', Rule::in(['pending', 'scheduled', 'canceled', 'done'])],
        ]);
        $appointment = $this->appointmentsFor($request)->findOrFail($id);

        if (isset($validated['user_id'])) {
            User::query()
                ->where('company_id', $request->user()->company_id)
                ->where('role', 'barber')
                ->findOrFail($validated['user_id']);
        }

        $appointment->update($validated);
        return response()->json($appointment);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->all();

        $this
            ->service
            ->cancel($request->user(), $data['id'], $data['reason']);

        return response()->json(null, 204);
    }

    public function done(Request $request)
    {
        $data = $request->all();

        $this
            ->service
            ->done($request->user(), $data['id'], $data['payment_method']);

        return response()->json(['message' => 'Agendamento concluído com sucesso!'], 200);
    }

    private function appointmentsFor(Request $request): Builder
    {
        $user = $request->user();

        return Appointment::query()
            ->whereHas('user', fn (Builder $query) => $query->where('company_id', $user->company_id))
            ->when(!$user->is_admin, fn (Builder $query) => $query->where('user_id', $user->id));
    }
}
