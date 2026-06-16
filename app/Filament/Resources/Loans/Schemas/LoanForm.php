<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('book_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('loan_date')
                    ->required(),
                DatePicker::make('due_date')
                    ->required(),
                Select::make('status')
                    ->options([
                        'borrowed' => 'Borrowed', 
                        'returned' => 'Returned',
                        'pending' => 'Pending',
                        'pending_return' => 'Pending Return',
                        'rejected' => 'Rejected',
                        ])
                    ->default('borrowed')
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }
}
