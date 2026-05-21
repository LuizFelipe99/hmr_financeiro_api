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
}