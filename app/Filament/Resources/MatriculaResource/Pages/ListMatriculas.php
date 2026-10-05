<?php

namespace App\Filament\Resources\MatriculaResource\Pages;

use App\Filament\Resources\MatriculaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMatriculas extends ListRecords
{
    protected static string $resource = MatriculaResource::class;

    protected static ?string $title = 'Control de Matrículas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Matrícula')
                ->icon('heroicon-o-plus'),
        ];
    }
}
