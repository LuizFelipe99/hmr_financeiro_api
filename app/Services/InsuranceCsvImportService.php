<?php

namespace App\Services;

use App\Models\CsvImport;
use App\Models\InsuranceTransaction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

class InsuranceCsvImportService
{
public function importFromCsv(UploadedFile $file, string $identifier): CsvImport
{
    $csvImport = $this->findOrCreateCsvImport($file, $identifier);

    $fileContent = file($file->getRealPath());
    $separator = $this->detectCsvSeparator($fileContent[0]);
    $csvData = array_map(fn($line) => str_getcsv($line, $separator), $fileContent);

    $rawHeaders = array_shift($csvData);
    $headers = array_map(fn($header) => trim(str_replace("\xEF\xBB\xBF", '', $header)), $rawHeaders);
    $totalColumns = count($headers);

    $validRows = array_filter($csvData, function ($row) {

        $firstColumn = strtoupper(trim($row[0] ?? ''));

        // ignora linhas vazias
        if (empty($firstColumn)) {
            return false;
        }

        // ignora cabeçalhos
        $invalidHeaders = [
            'SEGURADORAS',
            'SEGURADORA',
        ];

        if (in_array($firstColumn, $invalidHeaders)) {
            return false;
        }

        return true;
    });

    $csvImport->transactions()->delete();

    foreach ($validRows as $row) {
        $normalizedRow = array_slice(array_pad($row, $totalColumns, null), 0, $totalColumns);
        $rowData = array_combine($headers, array_map('trim', $normalizedRow));
        $this->createTransaction($csvImport->id, $rowData);
    }

    $csvImport->update([
        'status'     => 'processed',
        'total_rows' => count($validRows),
    ]);

    return $csvImport->fresh();
}

    private function findOrCreateCsvImport(UploadedFile $file, string $identifier): CsvImport
    {
        $existingImport = CsvImport::where('identifier', $identifier)->first();

        if ($existingImport) {
            return $existingImport;
        }

        return CsvImport::create([
            'identifier'     => $identifier,
            'file_name'      => $file->getClientOriginalName(),
            'file_extension' => $file->getClientOriginalExtension(),
            'file_size'      => $file->getSize(),
            'file_path'      => $file->store('insurance-imports'),
            'status'         => 'pending',
            'total_rows'     => 0,
        ]);
    }

    private function createTransaction(int $csvImportId, array $rowData): void
    {
        InsuranceTransaction::create([
            'csv_import_id'   => $csvImportId,
            'seguradora'      => $this->nullableString($rowData['SEGURADORAS'] ?? null),
            'data_vencimento' => $this->parseDate($rowData['DATA VENCIMENTO'] ?? null),
            'descricao'       => $this->nullableString($rowData['DESCRIÇÃO'] ?? null),
            'apolice'         => $this->nullableString($rowData['APOLICE'] ?? null),
            'ramo'            => $this->nullableString($rowData['RAMO'] ?? null),
            'parcela'         => $this->nullableString($rowData['PARCELA'] ?? null),
            'produtor'        => $this->nullableString($rowData['PRODUTOR'] ?? null),
            'parceiro'        => $this->nullableString($rowData['PARCEIRO'] ?? null),
            'valor_recebido'  => $this->parseDecimal($rowData['VALOR RECEBIDO'] ?? null),
            'observacoes'     => $this->nullableString($rowData['OBSERVAÇÕES'] ?? null),
            'origem'          => $this->nullableString($rowData['ORIGEM'] ?? null),
        ]);
    }

    private function nullableString(?string $value): ?string
    {
        if ($value === null) return null;
        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function parseDate(?string $dateString): ?string
    {
        if (!$dateString) return null;

        try { 
            return Carbon::createFromFormat('d/m/Y', trim($dateString))->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private function parseDecimal(?string $value): float
    {
        if (!$value) return 0.0;

        $cleaned = str_replace(['.', ' '], ['', ''], trim($value));
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    private function detectCsvSeparator(string $firstLine): string
    {
        $separators = [';', ',', '\t', '|'];

        $separatorCount = array_map(
            fn($separator) => substr_count($firstLine, $separator),
            array_combine($separators, $separators)
        );

        return array_key_first(
            array_filter($separatorCount, fn($count) => $count === max($separatorCount))
        );
    }
}