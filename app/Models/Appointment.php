<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'appointment_date',
        'appointment_time',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'appointment_service')
            ->withTimestamps();
    }


    public function fAppointmentTime(): Attribute
    {
        return Attribute::make(get: fn () => formatTime($this->appointment_time));
    }

    public function fAppointmentDate(): Attribute
    {
        return Attribute::make(get: fn () => formatDate($this->appointment_date));
    }

    public function fEstimatedTime(): Attribute
    {
        return Attribute::make(
            get: fn () => formatMinutesToHours($this->estimated_time)
        );
    }

    public function schedule(Collection $data): string
    {
        $employee = User::query()->findOrFail($data['employee_id']);
        $customer = Customer::query()->findOrFail($data['customer_id']);

        if ((int) $customer->company_id !== (int) $employee->company_id) {
            throw ValidationException::withMessages([
                'customer_id' => 'Cliente nao pertence a empresa do barbeiro selecionado.',
            ]);
        }

        if ($employee->role !== 'barber') {
            throw ValidationException::withMessages([
                'employee_id' => 'Usuario selecionado nao e um barbeiro.',
            ]);
        }

        $serviceIds = collect($data['services'])->pluck('id')->values();
        $services = Service::query()
            ->where('company_id', $employee->company_id)
            ->whereIn('id', $serviceIds)
            ->get();

        if ($services->count() !== $serviceIds->unique()->count()) {
            throw ValidationException::withMessages([
                'services' => 'Um ou mais servicos nao pertencem a empresa selecionada.',
            ]);
        }

        $this->user_id = $employee->id;
        $this->customer_id = $customer->id;
        $this->appointment_date = $data['appointment_date'];
        $this->appointment_time = $data['appointment_time'];
        $this->status = AppointmentStatus::PENDING_CONFIRMATION->value;
        $this->amount = $services->sum('price');

        $totalDuration = $services->sum('duration');

        $startTime = Carbon::parse($data['appointment_time']);
        $endTime = $startTime->copy()->addMinutes((int) $totalDuration);

        $start = $startTime->format('H:i:s');
        $end = $endTime->format('H:i:s');

        $isAvailable = AvailableSchedule::query()
            ->where('employee_id', $employee->id)
            ->where('date', $data['appointment_date'])
            ->where('start_time', '<=', $start)
            ->where('end_time', '>=', $end)
            ->exists();

        if (!$isAvailable) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Horario fora da disponibilidade do barbeiro.',
            ]);
        }

        $hasConflict = self::query()
            ->where('user_id', $employee->id)
            ->where('appointment_date', $data['appointment_date'])
            ->where('status', '!=', AppointmentStatus::CANCELED->value)
            ->where('appointment_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

        if ($hasConflict) {
            throw ValidationException::withMessages([
                'appointment_time' => 'Conflito de horario detectado.',
            ]);
        }

        $this->end_time = $end;
        $this->estimated_time = $totalDuration;
        $this->save();

        if (isset($data['services'])) {
            $syncData = $services->mapWithKeys(function (Service $service) {
                return [
                    $service->id => ['duration' => $service->duration]
                ];
            })->toArray();

            $this->services()->sync($syncData);
        }

        return 'Atendimento agendado com sucesso!';
    }
}
