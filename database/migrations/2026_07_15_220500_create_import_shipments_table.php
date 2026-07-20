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
        Schema::create('import_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('shipment_number')->unique();
            $table->foreignId('origin_port_id')->constrained('ports')->onDelete('cascade');
            $table->foreignId('destination_port_id')->constrained('ports')->onDelete('cascade');
            $table->string('status')->default('Pending');
            $table->string('transport_mode')->default('Sea Freight');
            $table->double('company_warehouse_lat')->nullable();
            $table->double('company_warehouse_lng')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_shipments');
    }
};
