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
        Schema::create('matriculas_exames', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('estudante_id');
            $table->unsignedBigInteger('exame_id');
            $table->enum('status', ['aguardando', 'em andamento', 'finalizado', 'expirado'])->default('aguardando');
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->timestamps();
            $table->integer('tentativas_permitidas')->default(1);
            $table->foreign('estudante_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exame_id')->references('id')->on('exames')->onDelete
            ('cascade');
            $table->unique(['estudante_id', 'exame_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('matriculas_exames');
    }
};
