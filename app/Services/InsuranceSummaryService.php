<?php

namespace App\Services;

use App\Models\InsuranceTransaction;
use Illuminate\Support\Collection;  

class InsuranceSummaryService
{
    public function getInsuranceSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null
    )
    {
        $query = InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId);

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                seguradora,

                SUM(
                    CASE
                        WHEN valor_recebido > 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as recebimentos,

                SUM(
                    CASE
                        WHEN valor_recebido < 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as pagamentos,

                SUM(valor_recebido) as liquido
            ')

            ->groupBy('seguradora')

            ->orderBy('seguradora')

            ->get();
    }
    public function getOriginSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null
    )
    {
        $query = InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId);

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                origem,

                SUM(
                    CASE
                        WHEN valor_recebido > 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as recebimentos,

                SUM(
                    CASE
                        WHEN valor_recebido < 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as pagamentos,

                SUM(valor_recebido) as liquido
            ')

            ->groupBy('origem')

            ->orderBy('origem')

            ->get();
    }

    public function getProducerSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null
    )
    {
        $query = InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId);

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                produtor,

                SUM(
                    CASE
                        WHEN valor_recebido > 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as recebimentos,

                SUM(
                    CASE
                        WHEN valor_recebido < 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as pagamentos,

                SUM(valor_recebido) as liquido
            ')

            ->groupBy('produtor')

            ->orderBy('produtor')

            ->get();
    }

    public function getPartnerSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null
    )
    {
        $query = InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId);

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                parceiro,

                SUM(
                    CASE
                        WHEN valor_recebido > 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as recebimentos,

                SUM(
                    CASE
                        WHEN valor_recebido < 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as pagamentos,

                SUM(valor_recebido) as liquido
            ')

            ->groupBy('parceiro')

            ->orderBy('parceiro')

            ->get();
    }

        public function getRamoSummary(
        int $csvImportId,
        ?string $dataInicio = null,
        ?string $dataFim = null
    )
    {
        $query = InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId);

        $this->applyDateFilter(
            $query,
            $dataInicio,
            $dataFim
        );

        return $query

            ->selectRaw('
                ramo,

                SUM(
                    CASE
                        WHEN valor_recebido > 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as recebimentos,

                SUM(
                    CASE
                        WHEN valor_recebido < 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as pagamentos,

                SUM(valor_recebido) as liquido
            ')

            ->groupBy('ramo')

            ->orderBy('ramo')

            ->get();
    }

    public function getSummaryByDateRange(int $csvImportId, string $dataInicio, string $dataFim): Collection {
        return InsuranceTransaction::query()
            ->where('csv_import_id', $csvImportId)
            ->whereBetween('data_vencimento', [
                $dataInicio,
                $dataFim
            ])
            ->selectRaw('
                DATE(data_vencimento) as data,
                SUM(
                    CASE
                        WHEN valor_recebido > 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as recebimentos,
                SUM(
                    CASE
                        WHEN valor_recebido < 0
                        THEN valor_recebido
                        ELSE 0
                    END
                ) as pagamentos,
                SUM(valor_recebido) as liquido
            ')

            ->groupBy('data')

            ->orderBy('data')

            ->get();
    }



    //helper filter date
    private function applyDateFilter($query,?string $dataInicio,?string $dataFim) {

        if ($dataInicio && $dataFim) {

            $query->whereBetween('data_vencimento', [
                $dataInicio,
                $dataFim
            ]);

        }

        return $query;
    }

public function getNewCustomersSummary(
    int $csvImportId,
    ?string $dataInicio = null,
    ?string $dataFim = null
)
{
    $query = InsuranceTransaction::query()

        ->where('csv_import_id', $csvImportId)

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

    // CLIENTES INDIVIDUAIS
    $clientes = (clone $query)

        ->select([
            'seguradora',
            'descricao',
            'parcela',
            'valor_recebido'
        ])

        ->orderByDesc('valor_recebido')

        ->get();

    // RESUMO POR SEGURADORA
    $seguradoras = (clone $query)

        ->selectRaw('
            seguradora,
            COUNT(*) as total_clientes,
            SUM(valor_recebido) as valor_total
        ')

        ->groupBy('seguradora')

        ->orderByDesc('valor_total')

        ->get();

    $totalGeral = $clientes->sum('valor_recebido');

    return [

        'total_geral' => round($totalGeral, 2),

        'total_clientes' => $clientes->count(),

        'seguradoras' => $seguradoras,

        'clientes' => $clientes

    ];
}


}