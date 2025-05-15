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
        Schema::create('log_atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tentativa_exame_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('evento')->nullable();
            $table->timestamp('registrado_em')->useCurrent();
            $table->ipAddress('ip')->nullable();
            $table->text('detalhes')->nullable();
            $table->timestamps(); // <- adiciona created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_atividades');
    }
};
