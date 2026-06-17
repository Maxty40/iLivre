<?php

namespace App\Filament\Resources\Books;

use App\Filament\Resources\Books\Pages\CreateBook;
use App\Filament\Resources\Books\Pages\EditBook;
use App\Filament\Resources\Books\Pages\ListBooks;
use App\Filament\Resources\Books\Schemas\BookForm;
use App\Filament\Resources\Books\Tables\BooksTable;
use App\Models\Book;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function getNavigationGroup(): ?string
    {
        return 'Library Management';
    }

    public static function form(Schema $schema): Schema
    {
        return BookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BooksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBooks::route('/'),
            'create' => CreateBook::route('/create'),
            'edit' => EditBook::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasPermissionTo('view-books');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasPermissionTo('create-book');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasPermissionTo('edit-book');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole('Admin') || auth()->user()->hasPermissionTo('delete-book');
    }
}
