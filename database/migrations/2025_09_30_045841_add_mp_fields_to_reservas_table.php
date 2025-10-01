<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('reservas', function (Blueprint $table) {
        $table->string('mp_preference_id')->nullable();
        $table->string('payment_url')->nullable();
        $table->string('payment_url_sandbox')->nullable();
        $table->string('mp_payment_id')->nullable();
        $table->string('payment_method')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            //
        });
    }
};
