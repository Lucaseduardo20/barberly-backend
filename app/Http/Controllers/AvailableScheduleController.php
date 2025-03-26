<?php

namespace App\Http\Controllers;

use App\Services\AvailableScheduleService;
use App\Data\AvailableScheduleData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailableScheduleController extends Controller
{
    public function __construct(protected AvailableScheduleService $scheduleService) {}

    /**
     * Listar os horários disponíveis do barbeiro autenticado.
     */
    public function index(): JsonResponse
    {
        $schedules = $this->scheduleService->listSchedules()->map(fn ($schedule) => AvailableScheduleData::fromData($schedule));
        return response()->json(['data' => $schedules]);
    }

    /**
     * Criar um novo horário disponível.
     */
    public function store(Request $request): JsonResponse
    {
        $result = $this->scheduleService->createSchedule(AvailableScheduleData::fromData($request->all()));

        return response()->json([
            'message' => $result['message'],
        ], $result['status']);
    }


    /**
     * Remover um horário disponível.
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->scheduleService->deleteSchedule($id);
        return response()->json(['message' => $result['message']], $result['status']);
    }
}
