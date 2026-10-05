<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalCustomer extends Model
{
    use HasFactory;

    protected $table = 'external_customers';

    public $timestamps = false;

    protected $fillable = [
        'cpf',
    ];

    /**
     * Check if a CPF exists in external customers
     */
    public static function exists(string $cpf): bool
    {
        return self::where('cpf', $cpf)->exists();
    }
}

