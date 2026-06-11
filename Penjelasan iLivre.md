##### Penjelasan ERD iLivre



1\. Roles dengan Users adalah One-to-Many (1:N), artinya satu role dapat dimiliki oleh banyak pengguna, tetapi satu pengguna hanya memiliki 1 role. FK-nya kolom role\_id pada tabel Users merujuk ke kolom id pada tabel Roles.



2\. Users dengan Membership\_cards adalah One-ton-one (1:1), artinya satu anggota hanya memiliki satu kartu anggota, begitupun sebaliknya. FK-nya kolom user\_id pada tabel membership\_cards yang merujuk ke kolom id pada users.



3\. Users dengan Loans adalah One-to-Many (1:N), artinya satu pengguna dapat melakukan banyak transaksi. FK-nya kolom user\_id pada tabel loans merujuk ke kolom id pada tabel users.



4\. Books dengan Loans adalah One-to-Many (1:N), artinya satu judul buku bisa dipinjam berulang kali dalam berbagai transaksi peminjaman yang berbeda. FK-nya kolom book\_id pada tabel loans merujuk ke kolom id pada tabel books.



5\. Loans dengan Returns adalah One-to-One (1:1), artinya setiap satu transaksi peminjaman hanya memiliki satu transaksi pengembalian. FK-nya kolom loan\_id pada tabel returns merujuk ke kolom id pada tabel loans.



##### Index



1. idx\_users\_name -> Fitur pencarian user berdasarkan nama untuk kebutuhan administrasi
2. idx\_member\_card -> Fitur pencarian user menggunakan kode kartu user untuk kebutuhan administrasi
3. idx\_books\_title -> Fitur pencarian buku berdasarkan judul untuk mempercepat pencarian pada katalog buku
4. idx\_loans\_status -> Fitur mempercepat proses filter data status peminjaman
5. idx\_loans\_due\_date -> Fitur mempercepar proses filter data tenggat waktu peminjaman



##### View



1. v\_active\_loans, penggabungan tabel loans, users, dan books untuk menampilkan nama peminjam, judul buku, dan tenggat waktu peminjaman. Use case: petugas/admin mengecek daftar buku yang sedang di pinjam.
2. v\_book\_catalog, untuk menampilkan data asli buku yang ada di perpustakaan, stok buku akan dikurangi jumlah buku yang sedang dipinjam. Use case: user melihat daftar buku yang tersedia.
3. v\_overdue\_loans, penggabungan tabel loans, return, users, dan books untuk menampilkan transaksi peminjaman yang lewat tenggat waktu pengembalian. Use case: petugas/admin mengirim email pengingat ke pengguna.



##### Stored Procedure



1. sp\_add\_new\_book, untuk menambah data buku baru ke katalog.
2. sp\_add\_book\_stock, untuk menambah jumlah stok pada satu judul buku yang terdaftar.
3. sp\_delete\_book, untuk menghapus data buku dari katalog. Data buku bisa dihapus jika buku tersebut tidak sedang dipinjam.
4. sp\_create\_loan, untuk menambahkan data peminjaman baru ke tabel loans. Jika buku sedang kosong, maka tidak bisa dipinjam.
5. sp\_get\_user\_total\_fines, untuk menghitung total denda dari anggota yang telat mengambalikan buku.



##### Triggers



1. trg\_after\_insert\_loans, setiap kali ada data baru masuk, stok buku akan berkurang.
2. trg\_after\_insert\_return, setiap kali ada data pengembalian buku, stok buku akan bertambah.
3. trg\_before\_insert\_loan, mencegah peminjaman apabila user masih memiliki buku yang belum dikembalikan.
4. trg\_before\_update\_book, untuk mencegah stok buku menjadi negatif pada saat peminjaman.
5. trg\_after\_delete\_loan, untuk mengembalikan stok buku apabila menghapus transaksi peminjaman.
6. trg\_process\_return\_details, untuk mengubah status peminjaman.



##### User Privileges

1. admin\_ilivre, memiliki akses SELECT, INSERT, UPDATE, dan DELETE pada database.
2. user\_ilivre, memiliki akses SELECT ke v\_book\_catalog. Hanya melihat buku saja.
3. officer\_ilivre, memiliki akses SELECT, INSERT, dan UPDATE ke tabel loans, returns, users, dan membership\_cards. Akses SELECT ke tabel books dan view\_book\_catalog.

