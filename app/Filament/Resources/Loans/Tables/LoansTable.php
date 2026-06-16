<?php

namespace App\Filament\Resources\Loans\Tables;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

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
            ->actions([
                Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        try {
                            DB::statement('CALL sp_approve_loan(?)', [$record->id]);

                            Notification::make()
                                ->title('Peminjaman disetujui')
                                ->success()
                                ->send();
                        } catch (QueryException $e) {
                            $errorMessage = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
                            Notification::make()
                                ->title('Gagal menyetujui peminjaman')
                                ->body($errorMessage)
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        try {
                            DB::statement('CALL sp_reject_loan(?)', [$record->id]);

                            Notification::make()
                                ->title('Peminjaman ditolak')
                                ->warning()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal menolak peminjaman')
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('approve_return')
                    ->label('Konfirmasi Pengembalian')
                    ->color('info')
                    ->icon('heroicon-o-arrow-path_rounded_square')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending_return')
                    ->action(function ($record) {
                        try {
                            // Memanggil Stored Procedure pengembalian yang lama
                            // Dia akan otomatis mengisi tabel returns, menghitung denda, dan men-trigger status jadi 'returned'
                            DB::statement('CALL sp_return_loan(?, ?)', [
                                $record->id,
                                $record->user_id,
                            ]);

                            Notification::make()
                                ->title('Pengembalian Buku Berhasil Dikonfirmasi')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Memproses Pengembalian')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
        }
    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
