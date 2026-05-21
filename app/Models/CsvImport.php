<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CsvImport extends Model
{
    protected $fillable = [
        'identifier',
        'file_name',
        'file_extension',
        'file_size',
        'file_path',
        'status',
        'total_rows',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(InsuranceTransaction::class);
    }
}