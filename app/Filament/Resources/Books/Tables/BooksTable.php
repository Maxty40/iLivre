<?php

namespace App\Filament\Resources\Books\Tables;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->select('books.*')
                    ->addSelect(['available_stock' => DB::table('v_book_catalog')
                        ->select('available_stock')
                        ->whereColumn('v_book_catalog.book_id', 'books.id')
                        ->limit(1)
                    ]);
            })
            ->columns([
                ImageColumn::make('cover_image')
                    ->disk('public')
                    ->label('Sampul Buku')
                    ->circular(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('author')
                    ->label('Penulis')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('publisher')
                    ->label('Penerbit')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('stock')
                    ->label('Total Stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('available_stock')
                    ->label('Stok Tersedia')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                ->visible(fn () => auth()->user()->hasRole('Admin')),
                ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
