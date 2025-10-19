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
        // Drop existing table if it exists with wrong structure
        Schema::dropIfExists('notifications');
        
        // Create notifications table with correct structure
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID as primary key
            $table->string('type'); // Notification class name
            $table->morphs('notifiable'); // notifiable_type and notifiable_id
            $table->text('data'); // JSON data
            $table->timestamp('read_at')->nullable(); // When notification was read
            $table->timestamps(); // created_at and updated_at
            
            // Add index for better query performance
            $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};