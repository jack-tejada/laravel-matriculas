<?php

namespace App\Filament\Widgets;

use App\Models\Matricula;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MatriculasOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalMatriculas = Matricula::count();
        $totalRecaudado = Matricula::where('estado', 'confirmado')->sum('monto');
        $pendientes = Matricula::where('estado', '!=', 'confirmado')->count();
        $totalUsuarios = User::count();

        return [
            Stat::make('Total Matrículas', $totalMatriculas)
                ->description('Estudiantes en el sistema')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->chart([7, 10, 14, 18, 22, 25, $totalMatriculas])
                ->color('primary'),

            Stat::make('Total Recaudado', 'S/ ' . number_format($totalRecaudado, 2))
                ->description('Matrículas pagadas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Matrículas Pendientes', $pendientes)
                ->description('En trámite o pendientes')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Usuarios Registrados', $totalUsuarios)
                ->description('Admin, Editor, Lector')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
