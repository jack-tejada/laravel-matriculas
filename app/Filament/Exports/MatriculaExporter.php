<?php

namespace App\Filament\Exports;

use App\Models\Matricula;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MatriculaExporter extends Exporter
{
    protected static ?string $model = Matricula::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('codigo')->label('Código'),
            ExportColumn::make('nombres')->label('Nombres'),
            ExportColumn::make('apellidos')->label('Apellidos'),
            ExportColumn::make('documento_tipo')->label('Tipo Doc'),
            ExportColumn::make('numero_documento')->label('N° Documento'),
            ExportColumn::make('email')->label('Correo Electrónico'),
            ExportColumn::make('telefono')->label('Teléfono'),
            ExportColumn::make('carrera')->label('Carrera / Programa'),
            ExportColumn::make('ciclo')->label('Ciclo'),
            ExportColumn::make('monto')->label('Monto Pagado'),
            ExportColumn::make('metodo_pago')->label('Método de Pago'),
            ExportColumn::make('estado')->label('Estado'),
            ExportColumn::make('fecha_matricula')->label('Fecha de Matrícula'),
            ExportColumn::make('observaciones')->label('Observaciones'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de matrículas ha finalizado con ' . number_format($export->successful_rows) . ' fila(s) exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' fila(s) fallaron al exportar.';
        }

        return $body;
    }
}
