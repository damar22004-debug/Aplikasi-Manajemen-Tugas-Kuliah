# 📋 Dashboard Management Tugas 

Dashboard manajemen tugas berbasis web dengan desain *Glassmorphism* modern untuk memantau progres perkuliahan secara efisien.

---

## 📄 Dokumentasi Project (Progress Report)

### 1. Deskripsi
Aplikasi ini adalah dashboard personal yang dirancang khusus bagi mahasiswa untuk mengelola tugas kuliah. Fokus utama aplikasi adalah pada antarmuka pengguna (UI) yang bersih, minimalis, dan transparan, memberikan pengalaman navigasi yang nyaman melalui visualisasi status tugas (Belum Mulai, Proses, Selesai, Terlambat).

### 2. User Story
- **Sebagai Mahasiswa**, saya ingin mencatat tugas baru beserta mata kuliah dan deadline-nya agar tidak ada tugas yang terlewat.
- **Sebagai Pengguna**, saya ingin melihat status progres tugas secara visual agar saya tahu prioritas mana yang harus dikerjakan lebih dulu.
- **Sebagai Pengguna**, saya ingin melihat kalender interaktif untuk memantau jadwal penting di bulan berjalan.

### 3. Software Requirements Specification (SRS)
#### Feature List:
- **Form Input Tugas:** Nama tugas, Mata Kuliah, dan Tanggal/Waktu.
- **Statistik Dashboard:** Penghitung otomatis jumlah tugas berdasarkan status.
- **Kalender Interaktif:** Navigator tanggal dengan indikator tugas.
- **Filter Status:** Memfilter tampilan kartu tugas berdasarkan kategori tertentu.
- **Manajemen Kartu:** Fitur untuk mengedit dan menghapus tugas yang sudah ada.

### 4. UML (Unified Modeling Language)
#### a. Use Case Diagram
- **Actor:** Mahasiswa (User).
- **Actions:** Create Task, View Dashboard, Update Task Status, Delete Task, View Calendar.

#### b. Activity Diagram
- Dimulai dari input form -> Validasi data -> Data disimpan ke LocalStorage -> Update UI Dashboard secara real-time.

#### c. Sequence Diagram
- User berinteraksi dengan UI Dashboard -> Dashboard memanggil fungsi simpan/edit -> Data diolah di JavaScript -> UI memberikan feedback instan kepada User.

### 5. Mock-Up
Desain menggunakan prinsip *Glassmorphism* dengan detail:
- **Sidebar:** Transparan dengan efek blur tinggi.
- **Cards:** Menggunakan border putih tipis dan bayangan lembut (soft shadow).
- **Warna:** Palet pastel untuk indikator status.

---

## 🛠️ SDLC (Software Development Life Cycle)
Proyek ini dikembangkan menggunakan metodologi **Agile (Scrum)**:
1. **Planning:** Menentukan fitur utama dan struktur data.
2. **Design:** Membuat mock-up UI/UX berbasis Glassmorphism.
3. **Implementation:** Coding menggunakan HTML5, Tailwind CSS, dan Vanilla JavaScript.
4. **Testing:** Uji coba fungsionalitas form input dan filter status.
5. **Deployment:** Hosting melalui GitHub Pages.

---

## 🚀 Teknologi yang Digunakan
- **Frontend:** HTML5, Tailwind CSS (Styling)
- **Icons:** Lucide Icons
- **Font:** Plus Jakarta Sans
- **Logic:** Vanilla JavaScript (ES6)

---

