<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FinancialSummaryService;
use App\Models\CsvImport;
use Illuminate\Http\JsonResponse;

class FinancialSummaryController extends Controller
{
    public function __construct(
        private readonly FinancialSummaryService $service
    ) {}

        public function getImports(): JsonResponse
    {
        return response()->json(
            CsvImport::select('id', 'identifier', 'file_name', 'status', 'total_rows', 'created_at')
                ->orderByDesc('created_at')
                ->get()
        );
    }

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

    public function origin(
        Request $request,
        int $csvImportId
    ) {

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',

            'categorias'   => 'nullable|array',
            'categorias.*' => 'string',

            'situacoes'    => 'nullable|array',
            'situacoes.*'  => 'string',
        ]);

        return response()->json(

            $this->service->originSummary(
                $csvImportId,
                $request->data_inicio,
                $request->data_fim,
                $request->categorias,
                $request->situacoes
            )

        );
    }

    public function partner(Request $request,int $csvImportId) 
    {

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',

            'categorias'   => 'nullable|array',
            'categorias.*' => 'string',

            'situacoes'   => 'nullable|array',
            'situacoes.*' => 'string',
        ]);

        return response()->json(

            $this->service->partnerSummary(
                $csvImportId,
                $request->input('data_inicio'),
                $request->input('data_fim'),
                $request->input('categorias'),
                $request->input('situacoes')
            )

        );
    }

    public function ramo(Request $request,int $csvImportId) 
    {

        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',

            'categorias'   => 'nullable|array',
            'categorias.*' => 'string',

            'situacoes'   => 'nullable|array',
            'situacoes.*' => 'string',
        ]);

        return response()->json(

            $this->service->ramoSummary(
                $csvImportId,
                $request->input('data_inicio'),
                $request->input('data_fim'),
                $request->input('categorias'),
                $request->input('situacoes')
            )

        );
    }

    // app/Http/Controllers/FinancialSummaryController.php

    public function summaryByDate(
        Request $request,
        int $csvImportId
    ) {
        $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim'    => 'nullable|date|after_or_equal:data_inicio',
            'categorias'  => 'nullable|array',
            'situacoes'   => 'nullable|array',
        ]);

        return response()->json(

            $this->service->summaryByDate(
                $csvImportId,
                $request->data_inicio,
                $request->data_fim,
                $request->categorias,
                $request->situacoes
            )

        );
    }

        public function newCustomers(
        Request $request,
        int $csvImportId
    ) {

        $request->validate([
            'group_by' => 'nullable|string',
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'categorias' => 'nullable|array',
            'categorias.*' => 'string',
            'situacoes' => 'nullable|array',
            'situacoes.*' => 'string',
        ]);

        return response()->json(

            $this->service->newCustomersSummary(
                $csvImportId,
                $request->group_by ?? 'fornecedor_cliente',
                $request->data_inicio,
                $request->data_fim,
                $request->categorias,
                $request->situacoes
            )

        );
    }
}