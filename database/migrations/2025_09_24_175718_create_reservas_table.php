<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservasTable extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('consola_id')->constrained('consolas')->onDelete('cascade');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedTinyInteger('horas'); // 1..3
            $table->decimal('precio_total', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, canceled
            $table->timestamps();

            $table->index(['consola_id', 'start_at', 'end_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
}
