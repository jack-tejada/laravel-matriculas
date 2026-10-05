<?php

namespace App\Filament\Resources;

use App\Filament\Exports\MatriculaExporter;
use App\Filament\Resources\MatriculaResource\Pages;
use App\Models\Matricula;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MatriculaResource extends Resource
{
    protected static ?string $model = Matricula::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $navigationLabel = 'Matrículas';

    protected static ?string $modelLabel = 'Matrícula';

    protected static ?string $pluralModelLabel = 'Matrículas';

    public static function canCreate(): bool
    {
        return auth()->user()?->canManageMatriculas() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canManageMatriculas() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Estudiante')
                    ->description('Datos personales y de contacto del postulante o alumno')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nombres')
                            ->label('Nombres')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('apellidos')
                            ->label('Apellidos')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('documento_tipo')
                            ->label('Tipo de Documento')
                            ->options([
                                'DNI' => 'DNI',
                                'Pasaporte' => 'Pasaporte',
                                'Carnet de Extranjería' => 'Carnet de Extranjería',
                            ])
                            ->default('DNI')
                            ->live()
                            ->required(),

                        Forms\Components\TextInput::make('numero_documento')
                            ->label('N° de Documento')
                            ->required()
                            ->rules(fn (Forms\Get $get) => $get('documento_tipo') === 'DNI' ? ['digits:8'] : ['string', 'max:20'])
                            ->validationMessages([
                                'digits' => 'El DNI debe contener exactamente 8 números (sin letras ni símbolos).',
                            ])
                            ->placeholder('Ej: 72341908')
                            ->maxLength(fn (Forms\Get $get) => $get('documento_tipo') === 'DNI' ? 8 : 20),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono / Celular')
                            ->tel()
                            ->rules(['nullable', 'digits:9'])
                            ->validationMessages([
                                'digits' => 'El celular debe tener exactamente 9 números (sin letras ni espacios).',
                            ])
                            ->placeholder('Ej: 987654321')
                            ->maxLength(9),
                    ]),

                Forms\Components\Section::make('Detalles de la Matrícula')
                    ->description('Programa académico, pagos y estado del trámite')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código de Matrícula')
                            ->default(fn () => 'MAT-' . date('Y') . '-' . str_pad((string) ((Matricula::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT))
                            ->helperText('Generado automáticamente correlativo (ej: MAT-2026-0001)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        Forms\Components\Select::make('carrera')
                            ->label('Carrera / Programa')
                            ->options([
                                'Desarrollo de Software' => 'Desarrollo de Software',
                                'Ciberseguridad y Redes' => 'Ciberseguridad y Redes',
                                'Diseño Gráfico Digital' => 'Diseño Gráfico Digital',
                                'Marketing y Negocios Digitales' => 'Marketing y Negocios Digitales',
                                'Inteligencia Artificial y Datos' => 'Inteligencia Artificial y Datos',
                            ])
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('ciclo')
                            ->label('Ciclo Académico')
                            ->options([
                                'I' => 'I Ciclo',
                                'II' => 'II Ciclo',
                                'III' => 'III Ciclo',
                                'IV' => 'IV Ciclo',
                                'V' => 'V Ciclo',
                                'VI' => 'VI Ciclo',
                            ])
                            ->default('I')
                            ->required(),

                        Forms\Components\TextInput::make('monto')
                            ->label('Monto Pagado (S/)')
                            ->numeric()
                            ->prefix('S/')
                            ->default(350.00)
                            ->required(),

                        Forms\Components\Select::make('metodo_pago')
                            ->label('Método de Pago')
                            ->options([
                                'Yape / Plin' => 'Yape / Plin',
                                'Transferencia BCP/BBVA' => 'Transferencia BCP/BBVA',
                                'Tarjeta Débito/Crédito' => 'Tarjeta Débito/Crédito',
                                'Efectivo en Caja' => 'Efectivo en Caja',
                            ])
                            ->default('Yape / Plin')
                            ->required(),

                        Forms\Components\Select::make('estado')
                            ->label('Estado de Matrícula')
                            ->options([
                                'confirmado' => 'Confirmado / Activo',
                                'en_proceso' => 'En Trámite',
                                'registrado' => 'Registrado (Pendiente Pago)',
                                'anulado' => 'Anulado',
                            ])
                            ->default('confirmado')
                            ->required(),

                        Forms\Components\DatePicker::make('fecha_matricula')
                            ->label('Fecha de Matrícula')
                            ->default(now())
                            ->required(),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones / Notas')
                            ->placeholder('Detalles de convalidación, becas, notas adicionales...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->label('Exportar a Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(MatriculaExporter::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nombres')
                    ->label('Estudiante')
                    ->formatStateUsing(fn ($record) => $record->nombres . ' ' . $record->apellidos)
                    ->description(fn ($record) => $record->documento_tipo . ': ' . $record->numero_documento)
                    ->searchable(['nombres', 'apellidos', 'numero_documento'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('carrera')
                    ->label('Carrera')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('ciclo')
                    ->label('Ciclo')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'success' => 'confirmado',
                        'warning' => 'en_proceso',
                        'info' => 'registrado',
                        'danger' => 'anulado',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmado' => 'Confirmado',
                        'en_proceso' => 'En Trámite',
                        'registrado' => 'Registrado',
                        'anulado' => 'Anulado',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_matricula')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'confirmado' => 'Confirmado',
                        'en_proceso' => 'En Trámite',
                        'registrado' => 'Registrado',
                        'anulado' => 'Anulado',
                    ]),

                Tables\Filters\SelectFilter::make('carrera')
                    ->label('Carrera')
                    ->options([
                        'Desarrollo de Software' => 'Desarrollo de Software',
                        'Ciberseguridad y Redes' => 'Ciberseguridad y Redes',
                        'Diseño Gráfico Digital' => 'Diseño Gráfico Digital',
                        'Marketing y Negocios Digitales' => 'Marketing y Negocios Digitales',
                        'Inteligencia Artificial y Datos' => 'Inteligencia Artificial y Datos',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                        ->label('Exportar Seleccionados a Excel')
                        ->exporter(MatriculaExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay matrículas registradas todavía')
            ->emptyStateDescription('La base de datos está limpia. Haz clic en el botón de abajo para registrar la primera matrícula.')
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Registrar Primera Matrícula')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMatriculas::route('/'),
            'create' => Pages\CreateMatricula::route('/create'),
            'edit' => Pages\EditMatricula::route('/{record}/edit'),
        ];
    }
}
