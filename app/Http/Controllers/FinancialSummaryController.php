<?php

namespace App\Http\Controllers;

use App\Services\FinancialSummaryService;
use Illuminate\Http\Request;

class FinancialSummaryController extends Controller
{
    public function __construct(
        private readonly FinancialSummaryService $service
    ) {}

    public function insurance(Request $request, ?int $csvImportId = null)
    {
        $summaries = $this->service->insuranceSummary(
        $csvImportId,
        $request->query('supplier')
    );

    return response()->json([
        'total_records'=> $summaries->count(),
        'data'=> $summaries,
    ]);
    }
}
