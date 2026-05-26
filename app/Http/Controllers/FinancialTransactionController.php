<?php

namespace App\Http\Controllers;

use App\Services\FinancialTransactionImportService;
use Illuminate\Http\Request;

class FinancialTransactionController extends Controller
{
    public function __construct(
        private readonly FinancialTransactionImportService $financialTransactionImportService
    ) {}

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'identifier' => 'required|string|max:255',
        ]);

        $result = $this->financialTransactionImportService->import(
    $request->file('file'),
    $request->identifier
);

return response()->json([
    'status' => 'success',
    'message' => 'Importação realizada com sucesso',
    'data' => $result
]);
    }

    public function index()
    {
        return response()->json(
            \App\Models\FinancialTransaction::query()
                ->latest()
                ->paginate(50)
        );
    }
}