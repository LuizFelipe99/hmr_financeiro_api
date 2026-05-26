<?php

namespace App\Services;

use App\Models\FinancialTransaction;

class FinancialSummaryService
{
    public function insuranceSummary(?int $csvImportId = null)
    {
        return FinancialTransaction::query()

            ->selectRaw('
                fornecedor_cliente,
                COUNT(*) as total_registros,
                SUM(valor) as total_valor
            ')

            ->when(
                $csvImportId,
                fn($query) => $query->where(
                    'csv_import_id',
                    $csvImportId
                )
            )

            ->groupBy('fornecedor_cliente')

            ->orderByDesc('total_valor')

            ->get();
    }
}