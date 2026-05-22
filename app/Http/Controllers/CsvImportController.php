<?php

namespace App\Http\Controllers;

use App\Services\CsvImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CsvImportController extends Controller
{
    public function __construct(
        private readonly CsvImportService $csvImportService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xls',
        ]);

        $results = $this->csvImportService->importUsersFromCsv(
            $request->file('file')
        );

        return response()->json([
            'message'  => 'Importação concluída',
            'imported' => $results['imported'],
            'skipped'  => $results['skipped'],
        ]);
    }
}