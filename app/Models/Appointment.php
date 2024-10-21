<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'service_id', 'customer_id', 'appointment_date', 'appointment_time', 'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function schedule (Collection $data): string
    {
        $this->employee_id = $data['employee_id'];
        $this->customer_id = $data['customer_id'];
        $this->service_id = $data['service_id'];
        $this->appointment_date = $data['appointment_date'];
        $this->appointment_time = $data['appointment_time'];
        $this->status = AppointmentStatus::PENDING_CONFIRMATION->value;
        $this->save();
        return 'Atendimento agendado com sucesso!';
    }
}
