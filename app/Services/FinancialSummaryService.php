<?php

namespace App\Services;

use App\Models\FinancialTransaction;

class FinancialSummaryService
{
    private const RECONCILED_STATUS = 'Conciliado';

    public function insuranceSummary(?int $csvImportId = null, ?string $supplierName = null)
    {
        return FinancialTransaction::query()
            ->selectRaw('
                fornecedor_cliente,
                COUNT(*) as total_registros,
                SUM(valor) as total_valor
            ')
            ->where('situacao', self::RECONCILED_STATUS)
            ->when(
                $csvImportId,
                fn ($query) => $query->where('csv_import_id', $csvImportId)
            )
            ->when(
                $supplierName,
                fn ($query) => $query->where('fornecedor_cliente', $supplierName)
            )
            ->groupBy('fornecedor_cliente')
            ->orderByDesc('total_valor')
            ->get();
    }


    //fornecedor / cliente/ seguradoras
    public function insuranceSummaryBySupplier(?int $csvImportId = null, ?string $supplierName = null)
    {
        return FinancialTransaction::query()
            ->selectRaw('
                fornecedor_cliente,
                SUM(
                    CASE
                        WHEN valor > 0
                        THEN valor
                        ELSE 0
                    END
                ) as total_recebimentos,
                ABS(SUM(
                    CASE
                        WHEN valor < 0
                        THEN valor
                        ELSE 0
                    END
                )) as total_pagamentos,
                SUM(valor) as total_liquido
            ')
            ->where('situacao', self::RECONCILED_STATUS)
            ->when(
                $csvImportId,
                fn ($query) => $query->where('csv_import_id', $csvImportId)
            )
            ->when(
                $supplierName,
                fn ($query) => $query->where('fornecedor_cliente', $supplierName)
            )
            ->groupBy('fornecedor_cliente')
            ->orderBy('fornecedor_cliente')
            ->get();
    }
    
}
