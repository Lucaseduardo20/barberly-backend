<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->integer('estimated_time');
            $table->enum('status', ['pending', 'scheduled', 'canceled', 'done'])->default('pending');
            $table->float('amount');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('payment_method')->nullable()->default(null);
            $table->string('reason')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('appointment_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->integer('duration');

            $table->unique(['appointment_id', 'service_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointment_service');
        Schema::dropIfExists('appointments');
    }
}
