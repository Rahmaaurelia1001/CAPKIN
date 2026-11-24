<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetPenarikanDanaResource\Pages;
use App\Models\TargetPenarikanDana;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class TargetPenarikanDanaResource extends Resource
{
    protected static ?string $model = TargetPenarikanDana::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $navigationLabel = 'Target Penarikan Dana';

    protected static ?string $modelLabel = 'Target Penarikan Dana';

    protected static ?string $pluralModelLabel = 'Target Penarikan Dana';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengelolaan Anggaran';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->role === 'katim_umper';
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $year = now()->year;
                        return collect(range($year - 5, $year + 5))
                            ->mapWithKeys(fn ($y) => [$y => $y])
                            ->toArray();
                    })
                    ->default(now()->year)
                    ->live()
                    ->required(),

                Select::make('bulan')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ])
                    ->unique(
                        table: 'target_penarikan_dana',
                        column: 'bulan',
                        modifyRuleUsing: fn ($rule, $get) => $rule->where('tahun', $get('tahun')),
                        ignoreRecord: true,
                    )
                    ->validationMessages([
                        'unique' => 'Target penarikan dana untuk bulan dan tahun ini sudah ada.',
                    ])
                    ->required(),

                TextInput::make('percent')
                    ->label('Target Penarikan Dana (%)')
                    ->numeric()
                    ->suffix('%')
                    ->rules(['required', 'numeric', 'min:0', 'max:100'])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('bulan')
                    ->label('Bulan')
                    ->formatStateUsing(fn ($state) => [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ][$state] ?? $state)
                    ->sortable(),

                TextColumn::make('percent')
                    ->label('Target (%)')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('tahun')
                    ->form([
                        Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                return TargetPenarikanDana::query()
                                    ->select('tahun')
                                    ->distinct()
                                    ->orderByDesc('tahun')
                                    ->pluck('tahun', 'tahun')
                                    ->toArray();
                            }),
                    ])
                    ->query(fn ($query, array $data) => $query->when(
                        $data['tahun'] ?? null,
                        fn ($q, $tahun) => $q->where('tahun', $tahun)
                    )),
            ])
            ->defaultSort('tahun', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTargetPenarikanDanas::route('/'),
            'create' => Pages\CreateTargetPenarikanDana::route('/create'),
            'edit' => Pages\EditTargetPenarikanDana::route('/{record}/edit'),
        ];
    }
}
