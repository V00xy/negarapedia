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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('country_code', 3);
            $table->string('country_name');
            $table->string('flag_url');
            $table->string('capital')->nullable();
            $table->bigInteger('population')->nullable();
            $table->string('currency')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};