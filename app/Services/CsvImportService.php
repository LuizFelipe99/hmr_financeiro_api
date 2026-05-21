<?php

namespace App\Services;

use App\Models\ImportedUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class CsvImportService
{
    public function importUsersFromCsv(UploadedFile $file): array
    {
        $fileContent = file($file->getRealPath());
        $separator = $this->detectCsvSeparator($fileContent[0]);
        $csvData = array_map(fn($line) => str_getcsv($line, $separator), $fileContent);
        $headers = array_map('trim', array_shift($csvData));

        $results = [
            'imported' => 0,
            'skipped'  => 0,
        ];

        foreach ($csvData as $row) {
            $trimmedRow = array_map('trim', $row);
            $rowData = array_combine($headers, $trimmedRow);

            if ($this->userAlreadyExists($rowData['e-mail'])) {
                $results['skipped']++;
                continue;
            }

            $this->createUser($rowData);
            $results['imported']++;
        }

        return $results;
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

    private function userAlreadyExists(string $email): bool
    {
        return ImportedUser::where('email', $email)->exists();
    }

    private function createUser(array $rowData): void
    {
        ImportedUser::create([
            'name'     => $rowData['nome'],
            'email'    => $rowData['e-mail'],
            'password' => Hash::make($rowData['password']),
            'nickname' => $rowData['nickname'],
        ]);
    }
}