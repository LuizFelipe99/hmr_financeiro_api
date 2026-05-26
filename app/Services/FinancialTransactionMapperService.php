<?php

namespace App\Services;

use Carbon\Carbon;

class FinancialTransactionMapperService
{
    public function map(
        array $headers,
        array $row,
        int $csvImportId
    ): array {

        $data = array_combine($headers, $row);

        return [

            'csv_import_id' => $csvImportId,

            'fornecedor_cliente' => $data['FORNECEDOR/CLIENTE'] ?? null,

            'data_vencimento' => $this->parseDate(
                $data['DATA VENCIMENTO'] ?? null
            ),

            'descricao' => $data['DESCRIÇÃO'] ?? null,

            'apolice' => $data['APOLICE'] ?? null,

            'ramo' => $data['RAMO'] ?? null,

            'parcela' => $data['PARCELA'] ?? null,

            'produtor' => $data['PRODUTOR'] ?? null,

            'parceiro' => $data['PARCEIRO'] ?? null,

            'tipo' => $data['TIPO'] ?? null,

            'conta_bancaria' => $data['CONTA BANCÁRIA'] ?? null,

            'situacao' => $data['SITUAÇÃO'] ?? null,

            'valor' => $this->parseMoney(
                $data['VALOR'] ?? 0
            ),

            'juros' => $this->parseMoney(
                $data['JUROS'] ?? 0
            ),

            'descontos' => $this->parseMoney(
                $data['DESCONTOS'] ?? 0
            ),

            'observacoes' => $data['OBSERVAÇÕES'] ?? null,

            'categoria' => $data['CATEGORIA'] ?? null,

            'origem' => $data['ORIGEM'] ?? null,
        ];
    }

    private function parseDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        try {

            return Carbon::createFromFormat(
                'd/m/Y',
                trim($date)
            )->format('Y-m-d');

        } catch (\Exception $e) {

            return null;
        }
    }

    private function parseMoney($value): float
    {
        if (!$value) {
            return 0;
        }

        $value = preg_replace('/[^\d,.-]/', '', $value);

        $value = str_replace('.', '', $value);

        $value = str_replace(',', '.', $value);

        return (float) $value;
    }
}