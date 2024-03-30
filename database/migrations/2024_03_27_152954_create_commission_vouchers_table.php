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
        Schema::create('commission_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->foreignId('request_id')->constrained();
            $table->json('voucher')->nullable();
            $table->foreignUuid('user_id');//the creator of the voucher
            $table->uuid('approver')->nullable();
            $table->string('issuing_type')->nullable();
            $table->string('issuer')->nullable();
            $table->string('transaction_reference_no')->nullable();
            $table->decimal('amount_transferred', 15,2)->nullable();
            $table->timestamps();

            $table->foreign('approver')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_vouchers');
    }
};
