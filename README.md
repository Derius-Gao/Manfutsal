# ManFutsal - Sistem Reservasi Lapangan Futsal

Sistem reservasi lapangan futsal online berbasis Laravel dengan role-based access control untuk memudahkan pengelolaan booking lapangan futsal.

## 🏆 Fitur Utama

### 🎯 Multi-Role System
- **Customer**: Register, login, cari lapangan, booking, upload pembayaran
- **Manager**: Konfirmasi booking, kelola keuangan, lihat activity log
- **Admin**: CRUD users/lapangan, kelola semua booking, laporan keuangan lengkap
- **SuperAdmin**: Hak akses management, pengaturan sistem, semua fitur admin

### 📱 Fitur Customer
- Pencarian lapangan berdasarkan lokasi dan waktu
- Booking online dengan perhitungan harga otomatis
- Upload bukti pembayaran
- Riwayat booking
- Notifikasi status booking
- Activity log personal

### 📊 Fitur Manager
- Dashboard dengan statistik booking
- Konfirmasi/reject booking
- Verifikasi pembayaran
- Laporan keuangan dengan grafik
- Activity log monitoring
- Export data

### ⚙️ Fitur Admin
- Manajemen user (CRUD, bulk actions)
- Manajemen lapangan (CRUD, foto, status)
- Monitoring semua booking
- Laporan keuangan komprehensif
- Export data berbagai format
- Advanced filtering dan search

### 🔧 Fitur SuperAdmin
- Manajemen role dan hak akses
- Pengaturan sistem (booking rules, payment, notification)
- Web configuration
- Backup dan restore
- System monitoring

## 🛠 Tech Stack

- **Backend**: Laravel 12.0, PHP 8.1+
- **Frontend**: Bootstrap 5, jQuery, SweetAlert2, Select2
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Breeze
- **Icons**: Font Awesome 6
- **Charts**: Chart.js
- **No Build Tools**: Pure CDN-based (no Vite/Webpack)

## 📋 Prerequisites

- PHP 8.1+ 
- MySQL/MariaDB
- Composer
- Web server (Apache/Nginx)
- PHP extensions: mbstring, openssl, pdo, tokenizer, xml

## 🚀 Quick Start

### 1. Clone Repository
```bash
git clone <repository-url>
cd manfutsal
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=manfutsal
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 6. Storage Link
```bash
php artisan storage:link
```

### 7. Start Development Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 👤 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@manfutsal.com | password |
| Admin | admin@manfutsal.com | password |
| Manager | manager@manfutsal.com | password |
| Customer | Register baru atau gunakan data dari seeder | password |

## 📁 Struktur Direktori

```
manfutsal/
├── app/
│   ├── Http/Controllers/
│   │   ├── Auth/              # Authentication controllers
│   │   ├── Customer/          # Customer controllers
│   │   ├── Manager/           # Manager controllers
│   │   ├── Admin/              # Admin controllers
│   │   └── SuperAdmin/        # SuperAdmin controllers
│   ├── Models/                # Eloquent models
│   ├── Helpers/               # Helper functions
│   └── Middleware/            # Custom middleware
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/              # Database seeders
│   └── factories/            # Model factories
├── resources/views/
│   ├── layouts/              # Base layouts
│   ├── auth/                 # Authentication views
│   ├── customer/             # Customer pages
│   ├── manager/              # Manager pages
│   ├── admin/                # Admin pages
│   └── superadmin/           # SuperAdmin pages
├── storage/app/public/        # File uploads
└── public/                   # Public assets
```

## 🎨 UI/UX Features

- **Responsive Design**: Optimal di desktop dan mobile
- **Modern Interface**: Clean dan professional dengan Bootstrap 5
- **Interactive Components**: SweetAlert2 untuk notifikasi, Select2 untuk dropdown
- **Real-time Updates**: Auto-refresh untuk data penting
- **Data Visualization**: Chart.js untuk grafik dan statistik
- **Role-based Navigation**: Menu dinamis berdasarkan user role

## 🔐 Security Features

- **Role-Based Access Control**: Middleware untuk proteksi route
- **Input Validation**: Server-side validation untuk semua input
- **CSRF Protection**: Built-in Laravel CSRF protection
- **SQL Injection Prevention**: Eloquent ORM protection
- **XSS Protection**: Laravel's built-in XSS protection
- **File Upload Security**: Validasi tipe dan ukuran file

## 📊 Database Schema

### Tables:
- `users` - Data user dengan role hierarchy
- `lapangan` - Informasi lapangan futsal
- `bookings` - Data booking dan status
- `payments` - Informasi pembayaran
- `activities` - Log aktivitas sistem

### Relationships:
- User → Bookings (One to Many)
- User → Activities (One to Many)
- Lapangan → Bookings (One to Many)
- Booking → Payment (One to One)

## 🧪 Testing

Lihat file `TESTING_GUIDE.md` untuk panduan testing lengkap.

### Quick Testing Commands:
```bash
# Test authentication
php artisan test --filter AuthenticationTest

# Test booking flow
php artisan test --filter BookingTest

# Test role access
php artisan test --filter RoleTest
```

## 📦 Deployment

### Production Setup:
1. Set `APP_ENV=production` dan `APP_DEBUG=false`
2. Optimize autoloader: `composer install --optimize-autoloader --no-dev`
3. Cache configuration: `php artisan config:cache`
4. Cache routes: `php artisan route:cache`
5. Set proper file permissions
6. Configure web server untuk Laravel

### Server Requirements:
- PHP 8.1+ dengan required extensions
- MySQL 5.7+ atau MariaDB 10.2+
- Web server dengan mod_rewrite
- Minimum 512MB RAM
- 1GB+ storage space

## 🔄 Maintenance

### Regular Tasks:
- Backup database harian/mingguan
- Monitor error logs
- Update dependencies
- Clean old logs dan temporary files
- Monitor storage usage

### Commands:
```bash
# Clear all caches
php artisan cache:clear

# Optimize database
php artisan db:optimize

# View system status
php artisan about
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

Proyek ini dilisensikan under MIT License - lihat file `LICENSE` untuk detail.

## 🆘 Support

Jika menemukan bug atau butuh bantuan:

1. Cek `TESTING_GUIDE.md` untuk troubleshooting
2. Search existing issues di repository
3. Buat issue baru dengan detail:
   - Langkah reproduksi
   - Expected vs actual behavior
   - Environment details
   - Error messages/screenshots

## 🎯 Roadmap

### Upcoming Features:
- [ ] SMS notification integration
- [ ] Payment gateway integration
- [ ] Advanced reporting
- [ ] Mobile app (React Native)
- [ ] API documentation
- [ ] Multi-language support
- [ ] Advanced analytics
- [ ] Email template customization

### Version History:
- **v1.0.0** - Initial release dengan core features
- **v1.1.0** - Enhanced reporting dan export
- **v1.2.0** - Mobile responsiveness improvements
- **v2.0.0** - Advanced analytics dan AI features (planned)

## 📞 Contact

- **Email**: info@manfutsal.com
- **Website**: https://manfutsal.example.com
- **Documentation**: https://docs.manfutsal.example.com

---

**Built with ❤️ using Laravel**
