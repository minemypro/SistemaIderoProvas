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
        Schema::create('exames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('disciplina');
            $table->enum('navegacao', ['sequencial', 'livre'])->default('livre');
            $table->integer('tentativas_permitidas')->default(1);
            $table->integer('duracao')->default(30);
            $table->enum('feedback', ['imediato', 'pos-analise'])->default('imediato');
            $table->integer('pontuacao_total')->nullable();
            $table->datetime('inicio');
            $table->datetime('fim');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }



    public function down(): void
    {
        Schema::dropIfExists('exames');
    }
};
