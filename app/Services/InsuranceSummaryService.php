<?php

namespace App\Services;

use App\Models\InsuranceTransaction;
use Illuminate\Support\Collection;

class InsuranceSummaryService
{
public function getInsuranceSummary(int $csvImportId)
{
    return InsuranceTransaction::query()

        ->where('csv_import_id', $csvImportId)

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

    public function getOriginSummary(int $csvImportId)
    {
        return InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId)

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

    public function getProducerSummary(int $csvImportId)
    {
        return InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId)

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

    public function getPartnerSummary(int $csvImportId)
    {
        return InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId)

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

        public function getRamoSummary(int $csvImportId)
    {
        return InsuranceTransaction::query()

            ->where('csv_import_id', $csvImportId)

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
}