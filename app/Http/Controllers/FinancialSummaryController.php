<?php

namespace App\Http\Controllers;

use App\Services\FinancialSummaryService;

class FinancialSummaryController extends Controller
{
    public function __construct(
        private readonly FinancialSummaryService $service
    ) {}

    public function insurance(?int $csvImportId = null)
    {
        return response()->json(
            $this->service->insuranceSummary($csvImportId)
        );
    }
}