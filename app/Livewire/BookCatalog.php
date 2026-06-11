<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class BookCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $sort = 'title';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $booksQuery = DB::table('v_book_catalog')
            ->where('title', 'like', '%' . $this->search . '%')
            ->orWhere('author', 'like', '%' . $this->search . '%')
            ->orderBy($this->sort, 'asc');

        return view('livewire.book-catalog', [
            'books' => $booksQuery->paginate(12)
        ]);
    }
}
