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
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_property')->unsigned();
            $table->timestamp('date')->nullable();
            $table->decimal('price',$precision = 8, $scale = 2)->nullable();
            $table->integer('minNight')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->foreign('id_property')->references('id')->on('properties')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar');
    }
};
