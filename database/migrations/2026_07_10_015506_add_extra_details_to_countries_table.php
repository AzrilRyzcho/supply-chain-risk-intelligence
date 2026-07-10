<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('official_name')->nullable()->after('name');
            $table->string('iso3_code', 3)->nullable()->after('code');
            $table->string('currency_symbol')->nullable()->after('currency_code');
            $table->string('capital')->nullable()->after('subregion');
            $table->text('timezone')->nullable()->after('area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['official_name', 'iso3_code', 'currency_symbol', 'capital', 'timezone']);
        });
    }
};
