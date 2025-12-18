Aplikasi Manajemen Tugas Kuliah
📌 Deskripsi Project
Aplikasi web untuk mengelola tugas perkuliahan dengan fitur CRUD, tracking deadline, prioritas tugas, dan statistik progress. Sistem membantu mahasiswa dalam mengorganisir tugas-tugas kuliah secara efektif.

👤 User Story
Sebagai Mahasiswa, saya ingin:

Login ke sistem dengan username dan password

Menambahkan tugas baru dengan detail lengkap

Melihat daftar semua tugas saya

Mengedit informasi tugas

Menghapus tugas yang sudah tidak relevan

Memfilter tugas berdasarkan status

Mengurutkan tugas berdasarkan deadline/prioritas

Melihat statistik progress tugas

Menerima notifikasi untuk deadline mendatang

Logout dari sistem dengan aman

📋 SRS (Software Requirements Specification)
Feature List:
1. Fitur Autentikasi (Authentication)
Login dengan username/password

Auto-register untuk user baru

Session management

Logout dengan session destroy

2. Fitur Manajemen Tugas (Assignment Management)
Create: Tambah tugas baru

Read: Lihat daftar tugas

Update: Edit tugas

Delete: Hapus tugas

Filter by status (Belum Mulai, Sedang Dikerjakan, Selesai, Terlambat)

Sort by deadline, priority, recency

Auto-update status terlambat

3. Fitur Dashboard & Statistik
Statistik cards (Total, Selesai, Dalam Proses, Terlambat)

Persentase penyelesaian

Real-time updates

Responsive design (Desktop & Mobile)

4. Fitur Notifikasi
Visual alert untuk tugas terlambat

Visual alert untuk deadline <24 jam

Countdown timer

Auto-check setiap 1 menit

5. Fitur UI/UX
Responsive design

Mobile-friendly (FAB, Bottom Sheet)

Color-coded badges

Form validation

Error handling


🔄 SDLC (Software Development Life Cycle)
1. Planning (Perencanaan)
Objective: Membuat sistem manajemen tugas untuk mahasiswa

Scope: Web-based application dengan CRUD functionality

Tools: PHP, MySQL, JavaScript, HTML/CSS

Timeline: 1-2 minggu development

2. Analysis (Analisis)
Requirement Gathering: User stories, feature list

Technical Feasibility: LAMP stack (Linux, Apache, MySQL, PHP)

Risk Assessment: Security, performance, usability

3. Design (Desain)
Architecture: Client-Server dengan REST API

Database Design: Normalized schema

UI/UX Design: Responsive, mobile-first

Security Design: Prepared statements, input sanitization

4. Implementation (Implementasi)
Frontend: HTML/CSS/JavaScript

Backend: PHP dengan MySQLi

Database: MySQL dengan 2 tables (users, assignments)

Integration: API endpoints, session management

5. Testing (Pengujian)
Unit Testing: Each function/component

Integration Testing: API endpoints

User Acceptance Testing: Fitur sesuai kebutuhan user

Security Testing: SQL injection, XSS protection

6. Deployment (Peluncuran)
Environment: Localhost development

Deployment: Upload ke web hosting

Documentation: README, user guide

Maintenance Plan: Bug fixes, feature updates

7. Maintenance (Pemeliharaan)
Bug Fixes: Monitor and fix issues

Updates: Security patches, feature enhancements

Support: User assistance, documentation updates

📈 Progress Status:
Planning & Analysis: 100%

Design (Database & UI): 100%

Implementation (Backend): 100%

Implementation (Frontend): 100%

Testing: 100%

Documentation: 100%

Deployment: In Progress

Maintenance: Ongoing
