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
        Schema::create('automation_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->string('title');
            $table->text('description');
            $table->string('assigned_to_role')->nullable();
            $table->uuid('creator');
            $table->unsignedBigInteger('days_before_due_date')->nullable();
            $table->unsignedBigInteger('sequence_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_tasks');
    }
};
