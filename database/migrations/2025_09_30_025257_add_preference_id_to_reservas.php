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
            if (! Schema::hasColumn('reservas', 'preference_id')) {
                $table->string('preference_id')->nullable()->after('payment_method');
            }
        });
    }
    public function down()
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'preference_id')) {
                $table->dropColumn('preference_id');
            }
        });
    }
};
