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
            $table->string('flag')->nullable()->after('region');
            $table->string('subregion')->nullable()->after('flag');
            $table->text('languages')->nullable()->after('subregion');
            $table->bigInteger('population')->nullable()->after('languages');
            $table->double('area')->nullable()->after('population');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn(['flag', 'subregion', 'languages', 'population', 'area']);
        });
    }
};
