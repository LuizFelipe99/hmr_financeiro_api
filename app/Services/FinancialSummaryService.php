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

            ->where(
                'csv_import_id',
                $csvImportId
            )

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

        $this->applyFilters(
            $query,
            $categorias,
            $situacoes
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

            ->whereNotNull('origem')

            ->where('origem', '!=', '')

            ->groupBy('origem')

            ->orderByDesc('liquido')

            ->get();
    }

    public function partnerSummary(
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

        $this->applyFilters(
            $query,
            $categorias,
            $situacoes
        );

        return $query

            ->selectRaw('
                parceiro,

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

            ->whereNotNull('parceiro')

            ->where('parceiro', '!=', '')

            ->groupBy('parceiro')

            ->orderByDesc('liquido')

            ->get();
    }

    public function ramoSummary(
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

        $this->applyFilters(
            $query,
            $categorias,
            $situacoes
        );

        return $query

            ->selectRaw('
                ramo,

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

            ->whereNotNull('ramo')

            ->where('ramo', '!=', '')

            ->groupBy('ramo')

            ->orderByDesc('liquido')

            ->get();
    }

    public function summaryByDate(
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

        $this->applyFilters(
            $query,
            $categorias,
            $situacoes
        );

        return $query

            ->selectRaw('
                DATE(data_vencimento) as data,

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

            ->groupBy('data')

            ->orderBy('data')

            ->get();
    }

    public function newCustomersSummary(
        int $csvImportId,
        string $groupBy = 'fornecedor_cliente',
        ?string $dataInicio = null,
        ?string $dataFim = null,
        ?array $categorias = null,
        ?array $situacoes = null
    ) {

        $allowedGroups = [
            'fornecedor_cliente',
            'origem',
            'produtor',
            'ramo',
            'parceiro'
        ];

        if (!in_array($groupBy, $allowedGroups)) {
            $groupBy = 'fornecedor_cliente';
        }

        $query = FinancialTransaction::query()

            ->where(
                'csv_import_id',
                $csvImportId
            )

            ->where(function ($query) {

                $query

                    ->whereRaw("
                        COALESCE(TRIM(parcela), '') = '1'
                    ")

                    ->orWhereRaw("
                        COALESCE(TRIM(parcela), '') LIKE '01%'
                    ")

                    ->orWhereRaw("
                        COALESCE(TRIM(parcela), '') LIKE '1/%'
                    ");
            });

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        $this->applyFilters(
            $query,
            $categorias,
            $situacoes
        );

        $clientes = (clone $query)

            ->select([
                $groupBy,
                'descricao',
                'parcela',
                'valor'
            ])

            ->orderByDesc('valor')

            ->get();

        $dados = (clone $query)

            ->selectRaw("
                {$groupBy} as nome,
                COUNT(*) as total_clientes,
                SUM(valor) as valor_total
            ")

            ->groupBy($groupBy)

            ->orderByDesc('valor_total')

            ->get();

        $totalGeral = $clientes->sum('valor');

        return [
            'group_by'       => $groupBy,
            'total_geral'    => round($totalGeral, 2),
            'total_clientes' => $clientes->count(),
            'dados'          => $dados,
            'clientes'       => $clientes
        ];
    }

    private function applyFilters(
        $query,
        ?array $categorias = null,
        ?array $situacoes = null
    ) {

        if (!empty($categorias)) {

            $query->whereIn(
                'categoria',
                $categorias
            );
        }

        if (!empty($situacoes)) {

            $query->whereIn(
                'situacao',
                $situacoes
            );
        }

        return $query;
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