<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodToReservas extends Migration
{
    public function up()
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (! Schema::hasColumn('reservas', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
}
