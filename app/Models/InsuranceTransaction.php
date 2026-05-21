<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceTransaction extends Model
{
    protected $fillable = [
        'csv_import_id',
        'seguradora',
        'data_vencimento',
        'descricao',
        'apolice',
        'ramo',
        'parcela',
        'produtor',
        'parceiro',
        'valor_recebido',
        'observacoes',
        'origem',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'valor_recebido'  => 'decimal:2',
    ];

    public function csvImport(): BelongsTo
    {
        return $this->belongsTo(CsvImport::class);
    }
}