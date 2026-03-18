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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_hash')->unique();
            $table->string('title');
            $table->enum('type', ['image', 'video']);
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            
            $table->string('google_drive_file_id')->nullable();
            $table->text('google_drive_url')->nullable();
            
            $table->jsonb('metadata')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->integer('view_count')->default(0);

            $table->timestamps();
            $table->softDeletes();
            
            // userstamps columns
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
