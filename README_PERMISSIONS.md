# Sistem Hak Akses Baru

## Overview

Sistem hak akses telah diubah dari role-based hierarchy menjadi permission-based per halaman dengan checkbox untuk setiap user.

## Fitur Baru

### 1. Hak Akses per User
- SuperAdmin otomatis memiliki akses ke semua halaman
- User lain dapat diatur hak aksesnya per halaman menggunakan checkbox
- Sistem lebih fleksibel dan granular

### 2. Halaman yang Dapat Diatur
- **Dashboard** - Halaman utama user
- **Users** - Manajemen data user
- **Lapangans** - Manajemen lapangan futsal
- **Bookings** - Manajemen pemesanan
- **Keuangan** - Laporan keuangan
- **Activities** - Log activity
- **Settings** - Pengaturan sistem
- **Access** - Manajemen hak akses (hanya superadmin)

### 3. Quick Actions
- **Select All** - Memberikan akses ke semua halaman
- **Deselect All** - Menghapus semua akses
- **Basic Access** - Akses dasar (dashboard + bookings)
- **Manager Access** - Akses manager (dashboard + bookings + keuangan + activities)

## Cara Menggunakan

1. Login sebagai SuperAdmin
2. Buka menu Hak Akses
3. Pilih user dari dropdown
4. Centang/hapus centang checkbox sesuai kebutuhan
5. Klik "Simpan Perubahan"

## Struktur Database

### Tabel `user_permissions`
- `user_id` - ID user
- `page_name` - Nama halaman
- `can_access` - Boolean (true/false)

## Middleware

### PermissionMiddleware
- Mengecek hak akses user ke halaman tertentu
- SuperAdmin dilewati (auto access)
- Redirect ke dashboard jika tidak memiliki akses

## Export Laporan Keuangan

Fitur export laporan keuangan telah diperbaiki dengan:

### Format Export
- **Excel** (.xlsx) - Dengan formatting dan auto-size columns
- **PDF** - Landscape orientation dengan styling
- **CSV** - UTF-8 BOM untuk Excel compatibility

### Filter Options
- Filter tanggal (start date & end date)
- Filter lapangan
- Include summary report
- Include transaction details

### Cara Export
1. Buka halaman Keuangan
2. Klik tombol "Export"
3. Pilih format dan filter
4. Klik "Export"
5. File akan otomatis di-download

## Dependencies

### Required Packages
```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

## Migration

Jalankan migration untuk membuat tabel `user_permissions`:
```bash
php artisan migrate
```

## Notes

- SuperAdmin tidak dapat diubah hak aksesnya (auto access ke semua halaman)
- Perubahan hak akses langsung berlaku setelah disimpan
- User akan di-redirect ke dashboard jika mencoba akses halaman tanpa izin
- Export data menggunakan real data dari database, bukan sample data
