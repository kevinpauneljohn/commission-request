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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->json('buyer');
            $table->string('project');
            $table->string('model_unit');
            $table->string('phase')->nullable();
            $table->string('block');
            $table->string('lot');
            $table->decimal('total_contract_price', $precision = 15, $scale = 2);
            $table->string('financing');
            $table->string('request_type');
            $table->string('sd_rate')->nullable();
            $table->integer('cheque_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('cheque_amount', $precision = 15, $scale = 2)->nullable();
            $table->text('message')->nullable();
            $table->foreignUuid('user_id')->nullable();
            $table->uuid('backend_user')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->foreign('backend_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
