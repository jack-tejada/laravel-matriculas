<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    protected $fillable = [
        'codigo',
        'nombres',
        'apellidos',
        'documento_tipo',
        'numero_documento',
        'email',
        'telefono',
        'carrera',
        'ciclo',
        'monto',
        'metodo_pago',
        'estado',
        'fecha_matricula',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha_matricula' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($matricula) {
            if (empty($matricula->codigo)) {
                $nextId = (static::max('id') ?? 0) + 1;
                $matricula->codigo = 'MAT-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
