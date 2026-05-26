<?php

namespace App\Http\Controllers;

use App\Services\FinancialTransactionImportService;
use Illuminate\Http\Request;

class FinancialImportController extends Controller
{
    public function __construct(
        private readonly FinancialTransactionImportService $financialImportService
    ) {}

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $result = $this->financialImportService->import(
            $request->file('file'),
            uniqid('import_')
        );

        return response()->json($result);
    }
}