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
        Schema::disableForeignKeyConstraints();

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('profile_id')->primary();
            $table->foreign('profile_id')->references('id')->on('profiles');
            $table->tinyInteger('votes');
            $table->text('content');
            $table->string('email', 50);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
