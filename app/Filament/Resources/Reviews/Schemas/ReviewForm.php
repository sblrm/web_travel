<?php

namespace App\Filament\Resources\Reviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('destination_id')
                    ->label('Destinasi')
                    ->relationship('destination', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled()
                    ->columnSpan(1),

                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled()
                    ->columnSpan(1),

                Select::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '⭐ 1 - Sangat Buruk',
                        2 => '⭐⭐ 2 - Buruk',
                        3 => '⭐⭐⭐ 3 - Cukup',
                        4 => '⭐⭐⭐⭐ 4 - Baik',
                        5 => '⭐⭐⭐⭐⭐ 5 - Sangat Baik',
                    ])
                    ->required()
                    ->columnSpan(1),

                Toggle::make('is_verified')
                    ->label('Terverifikasi')
                    ->default(false)
                    ->inline(false)
                    ->helperText('Review yang terverifikasi akan ditampilkan di website')
                    ->columnSpan(1),

                Textarea::make('comment')
                    ->label('Komentar')
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }
}
