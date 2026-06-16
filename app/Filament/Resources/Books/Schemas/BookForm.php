<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('author')
                    ->required(),
                TextInput::make('publisher'),
                TextInput::make('stock')
                    ->label('Total Stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                \Filament\Forms\Components\FileUpload::make('cover_image')
                    ->disk('public')
                    ->image()
                    ->directory('book-covers')
                    ->maxSize(2048),
            ]);
    }
}
