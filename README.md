# TP7DPBO2526C1

# JANJI
Saya Muhammad Ilal Zhafran dengan NIM 2502623 mengerjakan Tugas Praktikum 7 pada Mata Kuliah Desain dan Pemrograman Berorientasi Objek (DPBO) untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin

## TEMA
**Nama WEB:** HeartTalk
**Konsep:** Platform konseling dan curhat kesehatan emosional/asmara online.
**Fungsi Utama:** Memungkinkan anggota komunitas membagikan kisah curhatan mereka, menunjuk konselor spesialis yang sesuai, serta mengelola status respon cerita secara *real-time*.

## STRUKtUR

Aplikasi ini menggunakan database bernama **`db_hearttalk`** yang terdiri dari 3 entitas (tabel) yang saling berhubungan melalui relasi *Foreign Key*.

### 1. Tabel `users` (Anggota Komunitas)
Menyimpan data anggota atau pengguna yang mengirimkan cerita.
- `id` (INT, Primary Key, Auto Increment) — Identifier unik user.
- `nickname` (VARCHAR 100) — Nama panggilan user.
- `email` (VARCHAR 100, Unique) — Alamat email user.
- `age` (INT) — Umur user.

### 2. Tabel `counselors` (Konselor)
Menyimpan data konselor yang siap membantu pengguna.
- `id` (INT, Primary Key, Auto Increment) — Identifier unik konselor.
- `name` (VARCHAR 100) — Nama lengkap konselor.
- `specialty` (VARCHAR 100) — Keahlian/spesialisasi (misal: LDR, Ghosting, Overthinking).
- `email` (VARCHAR 100, Unique) — Alamat email konselor.

### 3. Tabel `stories` (Cerita Curhat — Tabel Relasi FK)
Menyimpan cerita yang diunggah user dan ditugaskan ke konselor tertentu.
- `id` (INT, Primary Key, Auto Increment) — Identifier unik cerita.
- `title` (VARCHAR 150) — Judul cerita curhat.
- `content` (TEXT) — Isi cerita lengkap.
- `user_id` (INT, Foreign Key) — Mengacu pada `users.id` (`ON DELETE CASCADE`).
- `counselor_id` (INT, Foreign Key) — Mengacu pada `counselors.id` (`ON DELETE CASCADE`).
- `status` (ENUM: `'Pending'`, `'Responded'`, `'Closed'`) — Status penanganan curhatan (Default: `'Pending'`).

## ALUR
Permintaan Klien (HTTP Request):
User membuka browser dan mengakses halaman tertentu (contoh: users.php atau stories.php).

Inisialisasi Database & Class Model:
File controller/view memanggil class/Database.php untuk menginisialisasi koneksi PDO db_hearttalk.
Objek entitas terkait (new User($db), new Counselor($db), atau new Story($db)) diinstansiasi dengan menyuntikkan (injecting) koneksi database ke constructor.

Eksekusi Aksi (Handling Action):
Jika ada form POST yang dikirim (create, update, atau delete), controller memanggil method yang sesuai pada objek class (misal: $userObj->create(...) atau $storyObj->delete(...)).

## Sesuai ketentuan pengerjaan:

PDO Prepared Statement: Seluruh interaksi database di dalam class (User.php, Counselor.php, dan Story.php) 100% menggunakan $this->conn->prepare() dan pengiriman parameter via bind parameter array / execute().

Bebas Query Mentah: Tidak ada eksekusi query mentah langsung seperti mysqli_query() atau $db->query() untuk proses olah data inputan user.

Foreign Key Integrity: Setiap aksi DELETE pada tabel parent (users / counselors) akan secara otomatis mengalirkan penghapusan ke tabel stories (ON DELETE CASCADE).

## DOKUMENTASI
