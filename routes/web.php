<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/loans', function(Illuminate\Http\Request $request) {
        $request->validate(['book_id' => 'required|integer']);
        
        try {
            Illuminate\Support\Facades\DB::statement('CALL sp_create_loan(?, ?)', [Illuminate\Support\Facades\Auth::id(), $request->book_id]);
            return redirect()->back()->with('success', 'Buku berhasil dipinjam!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal meminjam: ' . $e->getMessage());
        }
    })->name('loans.store');

    Route::post('/returns', function(Illuminate\Http\Request $request) {
        $request->validate(['loan_id' => 'required|integer']);
        Illuminate\Support\Facades\DB::table('returns')->insert([
            'loan_id' => $request->loan_id,
            'actual_return_date' => now(),
            'fine' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return redirect()->back()->with('success', 'Buku berhasil dikembalikan!');
    })->name('loans.return');
});

require __DIR__.'/auth.php';
