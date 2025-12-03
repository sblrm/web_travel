<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_code')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('destination_id')
                    ->relationship('destination', 'name')
                    ->required(),
                DatePicker::make('visit_date')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric(),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('visitor_name')
                    ->required(),
                TextInput::make('visitor_email')
                    ->email()
                    ->required(),
                TextInput::make('visitor_phone')
                    ->tel()
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'awaiting_payment' => 'Awaiting payment',
                        'paid' => 'Paid',
                        'verified' => 'Verified',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                    ])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('expires_at'),
                DateTimePicker::make('confirmed_at'),
                DateTimePicker::make('cancelled_at'),
                Textarea::make('cancellation_reason')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
