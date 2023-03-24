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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('client_id')->unique();
            $table->string('client_secret')->unique();
            $table->string('platform')->nullable();
            $table->string('platform_credentials')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->index('name');
            $table->index('client_id');
            $table->index('client_secret');
            $table->index('platform');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
