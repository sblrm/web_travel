<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpan(1),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->revealable()
                    ->maxLength(255)
                    ->placeholder(fn (string $context): string => $context === 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan password')
                    ->columnSpan(1),

                DateTimePicker::make('email_verified_at')
                    ->label('Email Terverifikasi Pada')
                    ->displayFormat('d M Y H:i')
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
