<?php

use App\Models\User;
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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user');
            $table->string("nom");
            $table->string("quartier")->nullable();
            $table->string("ville")->nullable();
            $table->string("pays")->nullable();
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->longText("description")->nullable();
            $table->string("image_principale")->nullable();
            // Info propiétés
            $table->boolean('meublee')->default(0);
            $table->string('type_propriete')->nullable();
            $table->integer('superficie')->default(0);
            $table->string('superficie_unite')->default("m²")->nullable();
            $table->integer('nombre_chambre')->default(0);
            $table->integer('nombre_bain')->default(0);
            $table->integer('nombre_parking')->default(0);
            $table->integer('nombre_personne_max')->default(0);
            $table->integer('minimum_reservation')->default(1);
            $table->integer('reduction_nombre_reservation')->default(1);
            $table->integer('reduction')->default(0);
            //
            $table->double('prix')->default(0)->nullable();
            $table->string('type_prix')->nullable();
            $table->integer("vue")->default(0)->nullable();
            $table->integer("partage")->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
