<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FinancialSummaryService;

class FinancialSummaryController extends Controller
{
    public function __construct(
        private readonly FinancialSummaryService $service
    ) {}

    public function insurance(
        Request $request,
        ?int $csvImportId = null
    ) {

        $summaries = $this->service->insuranceSummary(
            $csvImportId,
            $request->query('supplier')
        );

        return response()->json([
            'total_records' => $summaries->count(),
            'data'          => $summaries,
        ]);
    }

    // fornecedor / seguradoras
    public function insuranceBySupplier(
        Request $request,
        ?int $csvImportId = null
    ) {

        $summaries = $this->service->insuranceSummaryBySupplier(
            $csvImportId,
            $request->query('supplier')
        );

        return response()->json([
            'total_liquido' => $summaries->sum('total_liquido'),
            'data'          => $summaries,
        ]);
    }

    public function producer(
        Request $request,
        int $csvImportId
    ) {

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $summaries = $this->service->producerSummary(
            $csvImportId,
            $request->data_inicio,
            $request->data_fim
        );

        return response()->json([
            'total_records' => $summaries->count(),
            'data'          => $summaries,
        ]);
    }

    public function origin(Request $request,int $csvImportId) {
        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->service->originSummary(
                $csvImportId,
                $request->data_inicio,
                $request->data_fim
            )

        );
    }
}