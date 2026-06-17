<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLoansWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Peminjaman Terakhir';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Loan::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul Buku'),
                Tables\Columns\TextColumn::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'pending_return' => 'info',
                        'rejected' => 'danger',
                        'borrowed' => 'primary',
                        'approved' => 'success',
                        'returned' => 'secondary',
                        default => 'gray',
                    }),
            ])

            ->actions([]) // Kosongkan tombol aksi (edit/delete)
            ->bulkActions([]) // Kosongkan aksi massal
            ->recordUrl(null) // Matikan fitur klik baris biar nggak geser ke halaman edit
            ->paginated(false); // Matikan pagination biar rapi cuma nampilin 5 baris
    }
}
