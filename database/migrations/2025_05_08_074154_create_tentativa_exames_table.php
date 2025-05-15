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
        Schema::create('tentativa_exames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exame_id')->constrained()->onDelete('cascade');
            $table->integer('tentativa_numero');
            $table->timestamp('inicio');
            $table->timestamp('fim')->nullable();
            $table->float('nota_final')->nullable();
            $table->enum('status', ['aguardando', 'em andamento', 'finalizado', 'expirado'])->default('aguardando');
            $table->ipAddress('ip');
            $table->string('user_agent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tentativa_exames');
    }
};
