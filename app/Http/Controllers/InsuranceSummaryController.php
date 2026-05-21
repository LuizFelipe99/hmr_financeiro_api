<?php

namespace App\Http\Controllers;

use App\Services\InsuranceSummaryService;
use App\Models\CsvImport;
use Illuminate\Http\Request;

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

    public function producerSummary(?int $csvImportId = null)
    {
        if (!$csvImportId) {

            return response()->json([
                'status' => 'error',
                'message' => 'Id da importação é obrigatório'
            ], 400);
        }

        return response()->json(
            $this->insuranceSummaryService
                ->getProducerSummary($csvImportId)
        );
    }

    public function partnerSummary(?int $csvImportId = null)
    {
        if (!$csvImportId) {

            return response()->json([
                'status' => 'error',
                'message' => 'Id da importação é obrigatório'
            ], 400);
        }

        return response()->json(
            $this->insuranceSummaryService
                ->getPartnerSummary($csvImportId)
        );
    }

    public function ramoSummary(?int $csvImportId = null)
    {
        if (!$csvImportId) {

            return response()->json([
                'status' => 'error',
                'message' => 'Id da importação é obrigatório'
            ], 400);
        }

        return response()->json(
            $this->insuranceSummaryService
                ->getRamoSummary($csvImportId)
        );
    }

    public function summaryByDate(Request $request)
    {
        $request->validate([
            'csv_import_id' => 'required|integer',
            'data_inicio'   => 'required|date',
            'data_fim'      => 'required|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->insuranceSummaryService
                ->getSummaryByDateRange(
                    $request->csv_import_id,
                    $request->data_inicio,
                    $request->data_fim
                )

        );
    }
}