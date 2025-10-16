<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                       // requis
            $table->string('adresse')->nullable();       // texte nullable
            $table->string('code_postal')->nullable();   // texte nullable
            $table->string('ville')->nullable();         // texte nullable
            $table->string('lieu_de_tir')->nullable();   // texte nullable
            $table->string('telephone')->nullable();     // texte nullable
            $table->string('email')->nullable();         // texte nullable
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
