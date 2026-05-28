<?php

namespace App\Services;

use App\Models\FinancialTransaction;

class FinancialSummaryService
{
    private const RECONCILED_STATUS = 'Conciliado';

    public function insuranceSummary(
        ?int $csvImportId = null,
        ?string $supplierName = null
    ) {
        return FinancialTransaction::query()

            ->selectRaw('
                fornecedor_cliente,
                COUNT(*) as total_registros,
                SUM(valor) as total_valor
            ')

            ->where(
                'situacao',
                self::RECONCILED_STATUS
            )

            ->when(
                $csvImportId,
                fn($query) => $query->where(
                    'csv_import_id',
                    $csvImportId
                )
            )

            ->when(
                $supplierName,
                fn($query) => $query->where(
                    'fornecedor_cliente',
                    $supplierName
                )
            )

            ->groupBy('fornecedor_cliente')

            ->orderByDesc('total_valor')

            ->get();
    }

    // fornecedor / cliente / seguradoras
    public function insuranceSummaryBySupplier(
        ?int $csvImportId = null,
        ?string $supplierName = null
    ) {
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

            ->where(
                'situacao',
                self::RECONCILED_STATUS
            )

            ->when(
                $csvImportId,
                fn($query) => $query->where(
                    'csv_import_id',
                    $csvImportId
                )
            )

            ->when(
                $supplierName,
                fn($query) => $query->where(
                    'fornecedor_cliente',
                    $supplierName
                )
            )

            ->groupBy('fornecedor_cliente')

            ->orderBy('fornecedor_cliente')

            ->get();
    }

    public function producerSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ) {

        $query = FinancialTransaction::query()

            ->where('csv_import_id',$csvImportId)

            ->where(
                'situacao',
                self::RECONCILED_STATUS
            );

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                produtor,

                COUNT(*) as total_registros,

                SUM(
                    CASE
                        WHEN valor > 0
                        THEN valor
                        ELSE 0
                    END
                ) as recebimentos,

                ABS(SUM(
                    CASE
                        WHEN valor < 0
                        THEN valor
                        ELSE 0
                    END
                )) as pagamentos,

                SUM(valor) as liquido
            ')

            ->whereNotNull('produtor')
            ->where('produtor', '!=', '')
            ->groupBy('produtor')
            ->orderByDesc('liquido')
            ->get();
    }


    public function originSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null,
        ?array $categorias = null,
        ?array $situacoes = null
    ) {

        $query = FinancialTransaction::query()

            ->where(
                'csv_import_id',
                $csvImportId
            );

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                origem,

                COUNT(*) as total_registros,

                SUM(
                    CASE
                        WHEN valor > 0
                        THEN valor
                        ELSE 0
                    END
                ) as recebimentos,

                ABS(SUM(
                    CASE
                        WHEN valor < 0
                        THEN valor
                        ELSE 0
                    END
                )) as pagamentos,

                SUM(valor) as liquido
            ')

            ->when(
                $situacoes,
                fn ($query) => $query->whereIn(
                    'situacao',
                    $situacoes
                )
            )

            ->when(
                $categorias,
                fn ($query) => $query->whereIn(
                    'categoria',
                    $categorias
                )
            )

            ->whereNotNull('origem')

            ->groupBy('origem')

            ->orderByDesc('liquido')

            ->get();
    }
    private function applyDateFilter(
        $query,
        ?string $dataInicio,
        ?string $dataFim
    ) {

        if ($dataInicio && $dataFim) {

            $query->whereBetween(
                'data_vencimento',
                [
                    $dataInicio,
                    $dataFim
                ]
            );

            return $query;
        }

        if ($dataInicio) {

            $query->whereDate(
                'data_vencimento',
                '>=',
                $dataInicio
            );
        }

        if ($dataFim) {

            $query->whereDate(
                'data_vencimento',
                '<=',
                $dataFim
            );
        }

        return $query;
    }
}