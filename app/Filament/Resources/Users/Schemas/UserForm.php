<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Unit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('nip')
                    ->label('NIP')
                    ->unique(ignoreRecord: true)
                    ->maxLength(30),

                Select::make('role')
                ->label('Role / Hak Akses')
                ->options([
                    'admin' => 'Admin',
                    'pic' => 'PIC',
                    'kabid_kabag' => 'Kabid / Kabag',
                    'katim_umper' => 'Katim Umper',
                    'kabag_umum' => 'Kabag Umum',
                    'kapus' => 'Kapus',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set): void {
                    if (in_array($state, ['admin', 'kabag_umum', 'kapus'])) {
                    $set('unit_id', null);
                }
                })
                ->native(false),

            Select::make('unit_id')
                ->label('Unit Kerja')
                ->relationship('unit', 'nama')
                ->required(fn (Get $get): bool => ! in_array($get('role'), [
                    'admin',
                    'kabag_umum',
                    'kapus',
                ]))
                ->disabled(fn (Get $get): bool => in_array($get('role'), [
                    'admin',
                    'kabag_umum',
                    'kapus',
                ]))
                ->dehydrated(fn (Get $get): bool => ! in_array($get('role'), [
                    'admin',
                    'kabag_umum',
                    'kapus',
                ])),
                
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->minLength(8)
                    ->maxLength(255),
            ]);
    }
}