<?php

namespace App\Filament\Resources\Destinations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DestinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Destinasi')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->maxLength(255)
                    ->columnSpan(1),

                TextInput::make('slug')
                    ->label('Slug URL')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->columnSpan(1),

                FileUpload::make('images')
                    ->label('Gambar Destinasi')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->maxFiles(10)
                    ->maxSize(5120)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1080')
                    ->disk('public')
                    ->directory('destinations')
                    ->visibility('public')
                    ->downloadable()
                    ->previewable()
                    ->helperText('Upload maksimal 10 gambar (JPG/PNG, max 5MB per file). Rasio 16:9 direkomendasikan.')
                    ->columnSpanFull(),

                Select::make('province_id')
                    ->label('Provinsi')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('city')
                    ->label('Kota/Kabupaten')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(1),

                Select::make('category_id')
                    ->label('Kategori Budaya')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('latitude')
                    ->label('Latitude')
                    ->required()
                    ->numeric()
                    ->step(0.000001)
                    ->placeholder('-6.200000')
                    ->columnSpan(1),

                TextInput::make('longitude')
                    ->label('Longitude')
                    ->required()
                    ->numeric()
                    ->step(0.000001)
                    ->placeholder('106.816666')
                    ->columnSpan(1),

                TextInput::make('opening_hours')
                    ->label('Jam Buka')
                    ->required()
                    ->placeholder('08:00 atau 24 hours')
                    ->maxLength(50)
                    ->columnSpan(1),

                TextInput::make('closing_hours')
                    ->label('Jam Tutup')
                    ->required()
                    ->placeholder('17:00 atau 24 hours')
                    ->maxLength(50)
                    ->columnSpan(1),

                TextInput::make('est_visit_duration')
                    ->label('Estimasi Durasi (menit)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('menit')
                    ->default(60)
                    ->columnSpan(1),

                TextInput::make('ticket_price')
                    ->label('Harga Tiket')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->columnSpan(1),

                TextInput::make('rating')
                    ->label('Rating')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(5)
                    ->step(0.1)
                    ->default(0)
                    ->suffix('/ 5')
                    ->columnSpan(1),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(1),
            ])
            ->columns(2);
    }
}
