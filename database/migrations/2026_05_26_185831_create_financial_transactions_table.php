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
        Schema::create('financial_transactions', function (Blueprint $table) {

            $table->id();

            // RELAÇÃO IMPORTAÇÃO
            $table->foreignId('csv_import_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // DADOS PRINCIPAIS

            $table->string('fornecedor_cliente')
                ->nullable();

            $table->date('data_vencimento')
                ->nullable();

            $table->text('descricao')
                ->nullable();

            $table->string('apolice')
                ->nullable();

            $table->string('ramo')
                ->nullable();

            $table->string('parcela')
                ->nullable();

            $table->string('produtor')
                ->nullable();

            $table->string('parceiro')
                ->nullable();

            $table->string('tipo')
                ->nullable();

            $table->string('conta_bancaria')
                ->nullable();

            $table->string('situacao')
                ->nullable();

            // FINANCEIRO

            $table->decimal('valor', 15, 2)
                ->default(0);

            $table->decimal('juros', 15, 2)
                ->default(0);

            $table->decimal('descontos', 15, 2)
                ->default(0);

            // COMPLEMENTARES

            $table->text('observacoes')
                ->nullable();

            $table->string('categoria')
                ->nullable();

            $table->string('origem')
                ->nullable();

            // RAW CSV

            $table->json('raw_data')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | ÍNDICES
            |--------------------------------------------------------------------------
            */

            $table->index('csv_import_id');

            $table->index('data_vencimento');

            $table->index('fornecedor_cliente');

            $table->index('ramo');

            $table->index('produtor');

            $table->index('parceiro');

            $table->index('tipo');

            $table->index('situacao');

            $table->index('categoria');

            $table->index('origem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};