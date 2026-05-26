<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = [

        'csv_import_id',

        'fornecedor_cliente',
        'data_vencimento',
        'descricao',
        'apolice',
        'ramo',
        'parcela',
        'produtor',
        'parceiro',
        'tipo',
        'conta_bancaria',
        'situacao',
        'valor',
        'juros',
        'descontos',
        'observacoes',
        'categoria',
        'origem'

    ];

    protected $casts = [

        'data_vencimento' => 'date',
        'valor' => 'decimal:2',
        'juros' => 'decimal:2',
        'descontos' => 'decimal:2',

    ];
}