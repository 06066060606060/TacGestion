<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Colonnes métier
            $table->string('code', 64)->index();           // ex: code article interne [web:41]
            $table->string('reference', 128)->index();     // ex: référence fournisseur [web:41]
            $table->string('designation', 255);            // désignation courte [web:41]
			$table->string('classe', 64)->nullable();       //classe de risque
            $table->unsignedInteger('stock')->default(0);  // quantité en stock >= 0 [web:41]
            $table->decimal('poids_ma_kg', 10, 3)->nullable(); // poids moyen / U en kg [web:41]
            $table->timestamp('created_at')->nullable(); // date création du stock [web:41]
            $table->timestamp('updated_at')->nullable(); // date dernière mise à jour du stock [web:41]
            $table->string('emplacement', 255);  
            // Contexte / audits
            $table->softDeletes();           // deleted_at pour suppressions logiques [web:47][web:50]

            // Contraintes éventuelles
            // $table->unique(['code']);     // Décommentez si le code doit être unique [web:41]
            // $table->unique(['reference']); // Idem pour la référence [web:41]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
