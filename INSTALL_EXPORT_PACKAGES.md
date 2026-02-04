# Install Export Packages

Untuk fitur export Excel dan PDF, jalankan commands berikut:

## Install Laravel Excel
```bash
composer require maatwebsite/excel
```

## Install DOMPDF
```bash
composer require barryvdh/laravel-dompdf
```

## Publish Config (Optional)
```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## Run Migration
```bash
php artisan migrate
```

## Setelah install, restart server:
```bash
php artisan serve
```
