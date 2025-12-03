<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->required(),
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required(),
                TextInput::make('payment_code')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('account_holder_name')
                    ->default(null),
                TextInput::make('transfer_from')
                    ->default(null),
                FileUpload::make('proof_image')
                    ->image(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'uploaded' => 'Uploaded',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('paid_at'),
                DateTimePicker::make('verified_at'),
                TextInput::make('verified_by')
                    ->numeric()
                    ->default(null),
                Textarea::make('rejection_reason')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
