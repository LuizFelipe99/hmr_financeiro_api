<?php

namespace App\Http\Controllers;

use App\Services\InsuranceSummaryService;
use App\Models\CsvImport;

class InsuranceSummaryController extends Controller
{
    public function __construct(
        private readonly InsuranceSummaryService $insuranceSummaryService
    ) {}


    public function index(?int $csvImportId = null)
    {
        if (!$csvImportId) {

            return response()->json([
                'status' => 'error',
                'message' => 'Id da importação é obrigatório'
            ], 400);
        }

        $csvImport = CsvImport::find($csvImportId);

        if (!$csvImport) {

            return response()->json([
                'status' => 'error',
                'message' => 'Importação não encontrada'
            ], 404);
        }

        return response()->json(
            $this->insuranceSummaryService
                ->getInsuranceSummary($csvImportId)
        );
    }

    public function originSummary(?int $csvImportId = null)
    {
        if (!$csvImportId) {

            return response()->json([
                'status' => 'error',
                'message' => 'Id da importação é obrigatório'
            ], 400);
        }

        return response()->json(
            $this->insuranceSummaryService
                ->getOriginSummary($csvImportId)
        );
    }
}