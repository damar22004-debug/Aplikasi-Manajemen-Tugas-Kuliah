Aplikasi Manajemen Tugas Kuliah
📌 Deskripsi Project
Aplikasi web untuk mengelola tugas perkuliahan dengan fitur CRUD, tracking deadline, prioritas tugas, dan statistik progress. Sistem membantu mahasiswa dalam mengorganisir tugas-tugas kuliah secara efektif.

👤 User Story
Sebagai Mahasiswa, saya ingin:
a)	Login ke sistem dengan username dan password
b)	Menambahkan tugas baru dengan detail lengkap
c)	Melihat daftar semua tugas saya
d)	Mengedit informasi tugas
e)	Menghapus tugas yang sudah tidak relevan
f)	Memfilter tugas berdasarkan status
g)	Mengurutkan tugas berdasarkan deadline/prioritas
h)	Melihat statistik progress tugas
i)	Menerima notifikasi untuk deadline mendatang
j)	Logout dari sistem dengan aman

📋 SRS (Software Requirements Specification)
Feature List:
1. Fitur Autentikasi (Authentication)
•	Login dengan username/password
•	Auto-register untuk user baru
•	Session management
•	Logout dengan session destroy
2. Fitur Manajemen Tugas (Assignment Management)
•	Create: Tambah tugas baru
•	Read: Lihat daftar tugas
•	Update: Edit tugas
•	Delete: Hapus tugas
•	Filter by status (Belum Mulai, Sedang Dikerjakan, Selesai, Terlambat)
•	Sort by deadline, priority, recency
•	Auto-update status terlambat
3. Fitur Dashboard & Statistik
•	Statistik cards (Total, Selesai, Dalam Proses, Terlambat)
•	Persentase penyelesaian
•	Real-time updates
•	Responsive design (Desktop & Mobile)
4. Fitur Notifikasi
•	Visual alert untuk tugas terlambat
•	Visual alert untuk deadline <24 jam
•	Countdown timer
•	Auto-check setiap 1 menit
5. Fitur UI/UX
•	Responsive design
•	Mobile-friendly (FAB, Bottom Sheet)
•	Color-coded badges
•	Form validation
•	Error handling
