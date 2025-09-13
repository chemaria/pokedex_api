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
        Schema::create('pokemon', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 50)->unique();
            $table->enum('type', ['Electric', 'Fire', 'Water', 'Grass', 'Rock', 'Flying', 'Bug', 'Normal', 'Fighting', 'Poison', 'Ground', 'Psychic', 'Ice', 'Dragon', 'Dark', 'Steel', 'Fairy']);
            $table->integer('hp')->unsigned()->min(1)->max(100);
            $table->enum('status', ['wild', 'captured'])->default('wild');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};
