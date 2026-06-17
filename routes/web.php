<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    $activeCount = DB::table('loans')
        ->leftJoin('returns', 'loans.id', '=', 'returns.loan_id')
        ->where('loans.user_id', Auth::id())
        ->where('loans.status', 'approved')
        ->whereNull('returns.id')
        ->count();

    return view('dashboard', compact('activeCount'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/books/{id}', function ($id) {
    $book = DB::table('v_book_catalog')->where('book_id', $id)->first();
    if (!$book) abort(404);
    return view('book-detail', compact('book'));
})->name('books.show');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman Peminjaman & Riwayat
    Route::get('/loans', function () {
        $activeLoans = DB::table('loans')
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->leftJoin('returns', 'loans.id', '=', 'returns.loan_id')
            ->where('loans.user_id', Auth::id())
            ->whereIn('loans.status', ['approved', 'borrowed', 'pending_return'])
            ->whereNull('returns.id')
            ->select('loans.*', 'books.title', 'books.author')
            ->orderBy('loans.due_date', 'asc')
            ->get();

        $pendingLoans = DB::table('loans')
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->where('loans.user_id', Auth::id())
            ->where('loans.status', 'pending')
            ->select('loans.*', 'books.title', 'books.author')
            ->orderBy('loans.created_at', 'desc')
            ->get();

        // Paginate return history to optimize performance and memory footprint
        $returnHistory = DB::table('returns')
            ->join('loans', 'returns.loan_id', '=', 'loans.id')
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->where('loans.user_id', Auth::id())
            ->select('returns.*', 'loans.loan_date', 'loans.quantity', 'books.title', 'books.author')
            ->orderBy('returns.actual_return_date', 'desc')
            ->paginate(5); // Changed from get() to enable pagination semantics

        return view('loans', compact('activeLoans', 'pendingLoans', 'returnHistory'));
    })->name('loans.index');

    // Proses peminjaman baru
    Route::post('/loans', function (Request $request) {
        $request->validate([
            'book_id'  => 'required|integer',
            'quantity' => 'required|integer|min:1|max:3',
        ]);

        try {
            DB::statement('CALL sp_create_loan(?, ?, ?)', [
                Auth::id(),
                $request->book_id,
                $request->quantity,
            ]);
            return redirect()->back()->with('success', 'Permintaan peminjaman berhasil dikirim! Menunggu persetujuan petugas.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    })->name('loans.store');

    // Proses pengembalian buku
    Route::post('/returns', function (Request $request) {
        $request->validate(['loan_id' => 'required|integer']);

        try {
            DB::statement('CALL sp_request_return(?, ?)', [
                $request->loan_id,
                Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Pengajuan pengembalian berhasil dikirim! Silakan serahkan buku ke petugas.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    })->name('loans.return');
});

require __DIR__ . '/auth.php';
