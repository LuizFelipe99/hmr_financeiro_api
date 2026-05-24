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


    public function index( Request $request, ?int $csvImportId = null) {

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

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->insuranceSummaryService
                ->getInsuranceSummary(
                    $csvImportId,
                    $request->data_inicio,
                    $request->data_fim
                )

        );
    }




    

    public function originSummary(
        Request $request,
        ?int $csvImportId = null
    )
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

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->insuranceSummaryService
                ->getOriginSummary(
                    $csvImportId,
                    $request->data_inicio,
                    $request->data_fim
                )

        );
    }

    public function producerSummary(Request $request, ?int $csvImportId = null)
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

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->insuranceSummaryService
                ->getProducerSummary(
                    $csvImportId,
                    $request->data_inicio,
                    $request->data_fim
                )

        );
    }

    public function partnerSummary(Request $request, ?int $csvImportId = null)
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

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->insuranceSummaryService
                ->getPartnerSummary(
                    $csvImportId,
                    $request->data_inicio,
                    $request->data_fim
                )

        );
    }

    public function ramoSummary(Request $request, ?int $csvImportId = null)
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

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
        ]);

        return response()->json(

            $this->insuranceSummaryService
                ->getRamoSummary(
                    $csvImportId,
                    $request->data_inicio,
                    $request->data_fim
                )

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

public function newCustomersSummary(
    Request $request,
    ?int $csvImportId = null
)
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

    $request->validate([
        'group_by'   => 'nullable|string',
        'data_inicio' => 'nullable|date',
        'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
    ]);

    return response()->json(

        $this->insuranceSummaryService
            ->getNewCustomersSummary(
                $csvImportId,
                $request->group_by ?? 'seguradora',
                $request->data_inicio,
                $request->data_fim

            )

    );
}
}