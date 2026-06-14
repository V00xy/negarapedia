<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('country_code', 3);
            $table->string('country_name');
            $table->string('flag_url');
            $table->string('capital')->nullable();
            $table->bigInteger('population')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};