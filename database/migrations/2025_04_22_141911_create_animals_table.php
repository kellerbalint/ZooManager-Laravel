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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('species');
            $table->boolean('is_predator');
            $table->timestamp('born_at');
            $table->softDeletes();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('enclosure_id')->nullable();
            $table->foreign('enclosure_id')->references('id')->on('enclosures');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
