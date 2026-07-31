<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buysells', function (Blueprint $table) {
            $table->double('swap_charge')->default(0)->after('service_charge');
        });
    }

    public function down(): void
    {
        Schema::table('buysells', function (Blueprint $table) {
            $table->dropColumn('swap_charge');
        });
    }
};
