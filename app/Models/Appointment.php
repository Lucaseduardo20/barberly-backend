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
        $this->user_id = $data['employee_id'];
        $this->customer_id = $data['customer_id'];
        $this->appointment_date = $data['appointment_date'];
        $this->appointment_time = $data['appointment_time'];
        $this->status = AppointmentStatus::PENDING_CONFIRMATION->value;
        $this->amount = $data['amount'];

        $servicesData = collect($data['services']);
        $totalDuration = Service::whereIn('id', $servicesData->pluck('id'))->sum('duration');

        $startTime = Carbon::parse($data['appointment_time']);
        $endTime = $startTime->copy()->addMinutes((int) $totalDuration); // Note o uso de copy()

        $this->end_time = $endTime->format('H:i');
        $this->estimated_time = $totalDuration;
        $this->save();

        if (isset($data['services'])) {
            $syncData = $servicesData->mapWithKeys(function ($service) {
                return [
                    $service['id'] => ['duration' => $service['duration']]
                ];
            })->toArray();

            $this->services()->sync($syncData);
        }

        return 'Atendimento agendado com sucesso!';
    }
}
