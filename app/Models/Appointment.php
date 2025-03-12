<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Appointment extends Model
{
    use HasFactory;

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

    public function schedule(Collection $data): string
    {
        $this->user_id = $data['employee_id'];
        $this->customer_id = $data['customer_id'];
        $this->appointment_date = $data['appointment_date'];
        $this->appointment_time = $data['appointment_time'];
        $this->status = AppointmentStatus::PENDING_CONFIRMATION->value;
        $this->amount = $data['amount'];
        $this->estimated_time = $data['estimated_time'];
        $this->save();

        if (isset($data['service_ids'])) {
            $this->services()->sync($data['service_ids']);
        }

        return 'Atendimento agendado com sucesso!';
    }
}
