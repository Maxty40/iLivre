<?php

namespace App\Filament\Resources\Loans\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Username')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('book.title')
                    ->label('Judul Buku')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Tanggal Kembali')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'pending' => 'warning',
                        'pending_return' => 'info',
                        'rejected' => 'danger',
                        'borrowed' => 'primary',
                        'returned' => 'secondary',
                        default => null,
                    })
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
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
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        try {
                                DB::statement('CALL sp_approve_loan(?)', [$record->id]);

                            Notification::make()
                                ->title('Loan approved successfully')
                                ->success()
                                ->send();
                        } catch (QueryException $e) {
                            $errorMessage = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
                            Notification::make()
                                ->title('Failed to approve loan')
                                ->body($errorMessage)
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        try {
                            DB::statement('CALL sp_reject_loan(?)', [$record->id]);

                            Notification::make()
                                ->title('Loan rejected successfully')
                                ->warning()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Failed to reject loan')
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('mark_as_borrowed')
                    ->label('Tandai Sebagai Dipinjam')
                    ->color('primary')
                    ->icon('heroicon-o-book-open')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Peminjaman Buku')
                    ->modalDescription('Apakah Anda Yakin User Telah Meminjam Buku?')
                    ->visible(fn ($record) => $record->status === 'approved')
                    ->action(function ($record) {
                        $record->update(['status' => 'borrowed']);

                        Notification::make()
                            ->title('Peminjaman Disetujui')
                            ->success()
                            ->send();
                    }),

                Action::make('approve_return')
                    ->label('Konfirmasi Pengembalian')
                    ->color('info')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pengembalian')
                    ->modalDescription('Apakah Anda Yakin User Telah Mengembalikan Buku?')
                    ->visible(fn ($record) => $record->status === 'pending_return')
                    ->action(function ($record) {
                        try {
                            DB::statement('CALL sp_return_loan(?, ?)', [
                                $record->id,
                                $record->user_id,
                            ]);

                            Notification::make()
                                ->title('Buku Berhasil Dikembalikan')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Proses Pengembalian Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                EditAction::make()
                ->disabled(fn ($record) => in_array($record->status, ['pending', 'pending_return']))
                ->visible(fn ($record) => !in_array($record->status, ['pending', 'pending_return'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                //
            ]);
    }
}
