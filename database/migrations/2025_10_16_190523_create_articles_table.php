<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('reference')->nullable();
            $table->string('code_source')->nullable();
            $table->string('designation'); // requis pour indexation/affichage
            $table->string('type')->nullable();
            $table->string('famille')->nullable();
            $table->string('calibre')->nullable();
            $table->string('duree')->nullable();
            $table->string('categorie')->nullable();
            $table->string('certification')->nullable();
            $table->decimal('poids_m_a', 10, 3)->nullable(); // nombre avec 3 décimales
            $table->string('distance_securite')->nullable();
            $table->string('classe_risque')->nullable();
            $table->decimal('tarif_piece', 10, 2)->nullable();
            $table->string('cdt')->nullable();
            $table->decimal('tarif_caisse', 10, 2)->nullable();
            $table->string('rem')->nullable();
            $table->string('video')->nullable();
            $table->string('photo')->nullable();
            $table->string('provenance')->nullable();
            $table->string('note')->nullable();
             $table->string('options')->nullable();
            $table->timestamps();

            $table->index(['reference','designation']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
