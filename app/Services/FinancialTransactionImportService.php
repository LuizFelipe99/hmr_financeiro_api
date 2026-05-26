<?php

namespace App\Services;

use App\Models\CsvImport;
use App\Models\FinancialTransaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class FinancialTransactionImportService
{
    public function __construct(
        private readonly FinancialTransactionMapperService $mapper
    ) {}

    public function import(
        UploadedFile $file,
        string $identifier
    ): array {

        $storedPath = $file->store('imports');

        $csvImport = CsvImport::create([
            'identifier' => $identifier,
            'file_name' => $file->getClientOriginalName(),
            'file_extension' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'file_path' => $storedPath,
            'status' => 'processing',
            'total_rows' => 0
        ]);

        $fullPath = storage_path('app/private/' . $storedPath);

        $separator = $this->detectSeparator($fullPath);

        $csv = new SplFileObject($fullPath);

        $csv->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );

        $csv->setCsvControl($separator);

        $headers = [];

        $totalImported = 0;

        DB::beginTransaction();

        try {

            foreach ($csv as $index => $row) {

                if (!$row || $row === [null]) {
                    continue;
                }

                $row = array_map(
                    fn($value) => $this->sanitizeValue($value),
                    $row
                );

                if ($index === 0) {

                    $headers = $row;

                    continue;
                }

                if (count($headers) !== count($row)) {

                    continue;
                }

                $mappedData = $this->mapper->map(
                    $headers,
                    $row,
                    $csvImport->id
                );

                FinancialTransaction::create($mappedData);

                $totalImported++;
            }

            DB::commit();

            $csvImport->update([
                'status' => 'completed',
                'total_rows' => $totalImported
            ]);

            return [
                'success' => true,
                'total_importados' => $totalImported
            ];

        } catch (\Throwable $e) {

            DB::rollBack();

            $csvImport->update([
                'status' => 'error'
            ]);

            throw $e;
        }
    }

    private function detectSeparator(string $path): string
    {
        $file = fopen($path, 'r');

        $firstLine = fgets($file);

        fclose($file);

        $separators = [';', ',', '|', "\t"];

        $counts = [];

        foreach ($separators as $separator) {
            $counts[$separator] = substr_count($firstLine, $separator);
        }

        arsort($counts);

        return array_key_first($counts);
    }

    private function sanitizeValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = mb_convert_encoding(
            $value,
            'UTF-8',
            'UTF-8, ISO-8859-1, Windows-1252'
        );

        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);

        return trim($value);
    }
}