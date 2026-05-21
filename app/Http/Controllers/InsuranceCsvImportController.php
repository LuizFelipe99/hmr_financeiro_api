<?php

namespace App\Http\Controllers;

use App\Models\CsvImport;
use App\Services\InsuranceCsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsuranceCsvImportController extends Controller
{
    public function __construct(
        private readonly InsuranceCsvImportService $insuranceCsvImportService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file'       => 'required|file|mimes:csv,txt,xlsx',
            'identifier' => 'required|string|max:255',
        ]);

        $csvImport = $this->insuranceCsvImportService->importFromCsv(
            $request->file('file'),
            $request->input('identifier')
        );

        return response()->json([
            'message'    => 'Importação concluída',
            'identifier' => $csvImport->identifier,
            'total_rows' => $csvImport->total_rows,
            'status'     => $csvImport->status,
        ]);
    }

    public function index(): JsonResponse
    {
        return response()->json(
            CsvImport::select('id', 'identifier', 'file_name', 'status', 'total_rows', 'created_at')
                ->orderByDesc('created_at')
                ->get()
        );
    }
}