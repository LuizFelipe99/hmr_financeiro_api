<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('csv_import_id')->constrained('csv_imports')->cascadeOnDelete();
            $table->string('seguradora');
            $table->date('data_vencimento');
            $table->string('descricao');
            $table->string('apolice')->nullable();
            $table->string('ramo')->nullable();
            $table->string('parcela')->nullable();
            $table->string('produtor')->nullable();
            $table->string('parceiro')->nullable();
            $table->decimal('valor_recebido', 10, 2);
            $table->text('observacoes')->nullable();
            $table->string('origem');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_transactions');
    }
};