OURFLIX — FULL READY (XAMPP) [SIMPLE UPLOAD]
Netflix-style couple gallery (Public) + TailAdmin-style dashboard (Admin)

PUBLIC
- Netflix hero + search (TITLE only) + infinite scroll + mobile vertical feed
- Guest: view + comment (no login)

ADMIN
- Login/Logout
- Photos: upload/edit title+caption/delete
- Admins: create admin + change own username + own password (self-only)
- Comments: edit/delete semua komentar
- System check: cek folder uploads writable

DEFAULT ADMIN
- username: admin
- password: admin123

INSTALL (XAMPP)
1) Copy folder to: C:\xampp\htdocs\ourflix
2) Start Apache + MySQL
3) phpMyAdmin → create DB: ourflix
4) Import schema.sql  (WARNING: DROP TABLES)
5) Create default admin (RUN ONCE):
   http://localhost/ourflix/public/setup_admin.php?key=setup123
   then DELETE: public/setup_admin.php

OPEN
- Public: http://localhost/ourflix/public/
- Admin login: http://localhost/ourflix/public/login.php
- Admin dashboard: http://localhost/ourflix/public/admin/

UPLOAD NOTES
- Mode SIMPLE: file upload disimpan apa adanya (tanpa compress/thumbnail).
- Format: jpg/png/webp • max 8MB.
- Folder uploads: public/uploads (harus writable).


FIXES:
- Mobile: row cards sekarang scroll ke bawah (tidak horizontal) pada layar <= 640px.
- Admin: username disimpan lowercase; login juga lowercase agar tidak gagal karena huruf besar/kecil.


MOBILE UI (FINAL):
- Di mobile (<=768px) semua row foto menjadi GRID yang membungkus ke bawah.
- Jika foto bertambah, otomatis membuat baris baru (tanpa swipe kanan/kiri).


PUBLIC GRID MODE:
- Tampilan foto sekarang seperti Netflix/YouTube: kolom+baris (wrap otomatis) di semua ukuran layar.
- Tidak ada geser kanan/kiri.


ADMIN EDIT CREATED_AT:
- Admin bisa edit tanggal/waktu upload foto di halaman Edit Photo.


HERO EDITABLE:
- Text hero (judul, sub, tag) sekarang TIDAK dioverride JS.
- Bisa diedit langsung dari file public/index.php (HTML/PHP).


BRANDING:
- Ganti logo: replace file public/assets/img/logo.png
- Ganti nama/tagline: edit includes/config.php (SITE_NAME, SITE_TAGLINE)


ADMIN:
- Edit Photo: admin bisa ubah tanggal & waktu upload (Created).
