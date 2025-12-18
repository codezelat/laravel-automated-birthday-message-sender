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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g., 'SMS Sent', 'Import', 'Manual Trigger'
            $table->text('description')->nullable();
            $table->string('status')->default('info'); // success, error, info
            $table->json('payload')->nullable(); // Store extra data like contact ID, response body
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
