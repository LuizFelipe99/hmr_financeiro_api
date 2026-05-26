<?php

namespace App\Services;

use App\Mappers\FinancialTransactionMapper;
use App\Models\CsvImport;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FinancialImportService
{
    public function import(string $filePath): array
    {
        $rows = Excel::toArray([], $filePath);

        if (!isset($rows[0]) || count($rows[0]) <= 1) {

            return [
                'success' => false,
                'message' => 'Planilha vazia'
            ];
        }

        $sheet = $rows[0];

        $headers = array_shift($sheet);

        $headers = array_map(function ($header) {

            return trim($header);

        }, $headers);

        DB::beginTransaction();

        try {

            $csvImport = CsvImport::create([
                'file_name' => basename($filePath)
            ]);

            $imported = 0;

            foreach ($sheet as $row) {

                if (count(array_filter($row)) === 0) {
                    continue;
                }

                $rowAssociative = array_combine($headers, $row);

                $mapped = FinancialTransactionMapper::map(
                    $rowAssociative
                );

                $mapped['csv_import_id'] = $csvImport->id;

                FinancialTransaction::create($mapped);

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'csv_import_id' => $csvImport->id,
                'imported' => $imported
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}