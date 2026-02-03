# Testing & Debugging Guide - ManFutsal Reservation System

## 📋 Testing Checklist

### ✅ Prerequisites
- [ ] PHP 8.1+ terinstall
- [ ] MySQL/MariaDB terinstall dan running
- [ ] Composer terinstall
- [ ] Web server (Apache/Nginx) terinstall
- [ ] Database `manfutsal` sudah dibuat

### ✅ Setup Testing
- [ ] `.env` file sudah dikonfigurasi dengan benar
- [ ] Database connection berhasil
- [ ] Migration berhasil dijalankan
- [ ] Seeder berhasil dijalankan
- [ ] Storage folder sudah writable (`storage/app/public`)
- [ ] Vendor dependencies sudah terinstall

### ✅ Authentication Testing
- [ ] **Register Customer**
  - [ ] Form validasi berjalan
  - [ ] Email unik validation
  - [ ] Password confirmation
  - [ ] Default role = customer
  - [ ] Redirect ke dashboard customer setelah register

- [ ] **Login**
  - [ ] Email dan password validation
  - [ ] Redirect berdasarkan role:
    - [ ] Customer → `customer.dashboard`
    - [ ] Manager → `manager.dashboard`
    - [ ] Admin → `admin.dashboard`
    - [ ] SuperAdmin → `superadmin.dashboard`
  - [ ] Error handling untuk kredensial salah
  - [ ] Remember me functionality

- [ ] **Logout**
  - [ ] Session di-clear
  - [ ] Redirect ke login page

### ✅ Role-Based Access Testing
- [ ] **Customer Access**
  - [ ] Hanya bisa akses route customer.*
  - [ ] Tidak bisa akses manager.*, admin.*, superadmin.*
  - [ ] Redirect ke dashboard customer jika mencoba akses forbidden route

- [ ] **Manager Access**
  - [ ] Bisa akses customer.* (inherit)
  - [ ] Bisa akses manager.*
  - [ ] Tidak bisa akses admin.*, superadmin.*

- [ ] **Admin Access**
  - [ ] Bisa akses customer.*, manager.* (inherit)
  - [ ] Bisa akses admin.*
  - [ ] Tidak bisa akses superadmin.*

- [ ] **SuperAdmin Access**
  - [ ] Bisa akses semua route

### ✅ Customer Feature Testing
- [ ] **Dashboard**
  - [ ] Stat cards muncul dengan data
  - [ ] Quick actions berfungsi

- [ ] **Cari Lapangan**
  - [ ] Filter berfungsi (nama, daerah, tanggal)
  - [ ] Grid view menampilkan lapangan
  - [ ] Booking modal muncul
  - [ ] Perhitungan harga otomatis
  - [ ] Form validation

- [ ] **Booking List**
  - [ ] Tabel menampilkan booking user
  - [ ] Filter berfungsi
  - [ ] Upload bukti pembayaran
  - [ ] Status badges
  - [ ] Cancel booking (jika memungkinkan)

- [ ] **Activity Log**
  - [ ] Timeline menampilkan aktivitas
  - [ ] Filter berfungsi
  - [ ] Auto-refresh

### ✅ Manager Feature Testing
- [ ] **Dashboard**
  - [ ] Stat cards untuk booking
  - [ ] Quick actions berfungsi

- [ ] **Konfirmasi Booking**
  - [ ] Tabel menampilkan semua booking
  - [ ] Customer info muncul
  - [ ] Payment verification
  - [ ] Confirm/Reject/Complete actions
  - [ ] Bulk actions

- [ ] **Keuangan**
  - [ ] Charts muncul dan interaktif
  - [ ] Summary cards
  - [ ] Filter date range
  - [ ] Export functionality

- [ ] **Activity Log**
  - [ ] Timeline view
  - [ ] Real-time updates
  - [ ] Export functionality

### ✅ Admin Feature Testing
- [ ] **Dashboard**
  [ ] Stat cards untuk semua data
  [ ] Quick actions berfungsi

- [ ] **User Management**
  [ ] CRUD operations
  [ ] Bulk actions (select all, delete, activate)
  [ ] Role assignment
  [ ] Status toggle
  [ ] Search dan filter

- [ ] **Lapangan Management**
  [ ] CRUD operations
  [ ] Image upload
  [ ] Status toggle
  [ ] Filter by daerah/status
  [ ] Grid view

- [ ] **Booking Management**
  [ ] View semua booking
  [ ] Edit booking
  [ ] Status management
  [ ] Filter berbagai kriteria
  [ ] Export functionality

- [ ] **Keuangan**
  [ ] Advanced charts
  [ ] Payment method statistics
  [ ] Multi-format export
  [ ] Comprehensive filtering

### ✅ SuperAdmin Feature Testing
- [ ] **Dashboard**
  [ ] Overview semua sistem
  [ ] Quick actions ke semua fitur

- [ ] **Hak Akses Management**
  [ ] Role hierarchy visualization
  [ ] User role change
  [ ] Role change history
  [ ] Export role data

- [ ] **Web Setting**
  [ ] Multi-tab configuration
  [ ] Auto-save functionality
  [ ] Reset to default
  [ ] Test settings
  [ ] Export/backup

### ✅ Security Testing
- [ ] **Authentication**
  [ ] SQL injection protection
  [ ] XSS protection
  [ ] CSRF protection
  [ ] Session security

- [ ] **Authorization**
  [ ] Role-based access control
  [ ] Middleware protection
  [ ] Route protection

- [ ] **Input Validation**
  [ ] Form validation di semua input
  [ ] File upload validation
  [ ] Data type validation

### ✅ Performance Testing
- [ ] **Page Load Speed**
  [ ] Dashboard < 2 detik
  [ ] Table dengan data < 3 detik
  [ ] Charts rendering < 2 detik

- [ ] **Database Queries**
  [ ] Tidak ada N+1 queries
  [ ] Index optimization
  [ ] Query optimization

- [ ] **Frontend**
  [ ] JavaScript tidak error
  [ ] CSS loading properly
  [ ] Responsive design

### ✅ Browser Compatibility
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### ✅ Mobile Responsiveness
- [ ] Dashboard responsive
- [ ] Forms usable di mobile
- [ ] Tables scrollable
- [ ] Navigation works

## 🐛 Common Issues & Solutions

### Database Issues
**Problem:** Connection failed
```
SQLSTATE[HY000] [2002] Connection refused
```
**Solution:**
1. Check MySQL service running
2. Verify `.env` database credentials
3. Check database name exists
4. Verify user permissions

**Problem:** Migration failed
```
SQLSTATE[42S01]: Base table or view already exists
```
**Solution:**
1. Run `php artisan migrate:rollback`
2. Run `php artisan migrate:fresh --seed`
3. Check for existing tables

### Authentication Issues
**Problem:** Login redirect loop
**Solution:**
1. Clear cache: `php artisan cache:clear`
2. Check session configuration
3. Verify middleware registration

**Problem:** Role middleware not working
**Solution:**
1. Check `bootstrap/app.php` middleware registration
2. Verify role values in database
3. Check user is_active status

### File Upload Issues
**Problem:** File upload failed
**Solution:**
1. Check `storage/app/public` permissions
2. Run `php artisan storage:link`
3. Verify upload_max_filesize in php.ini

### Frontend Issues
**Problem:** JavaScript not working
**Solution:**
1. Check browser console for errors
2. Verify jQuery loaded before other scripts
3. Check for script conflicts

**Problem:** CSS not loading
**Solution:**
1. Check CDN links
2. Verify asset publishing
3. Clear browser cache

## 🔧 Debugging Tools

### Laravel Debugging
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check routes
php artisan route:list

# Check migrations
php artisan migrate:status

# Create test user
php artisan tinker
>>> User::factory()->create(['role' => 'customer']);
```

### Database Debugging
```sql
-- Check users
SELECT * FROM users;

-- Check role distribution
SELECT role, COUNT(*) as count FROM users GROUP BY role;

-- Check recent bookings
SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10;
```

### Browser Debugging
1. Open Developer Tools (F12)
2. Check Console tab for JavaScript errors
3. Check Network tab for failed requests
4. Check Elements tab for CSS issues

## 📊 Performance Monitoring

### Key Metrics to Monitor
- Page load time
- Database query time
- Memory usage
- CPU usage
- Error rate

### Tools
- Laravel Telescope (if installed)
- Laravel Debugbar (for development)
- Browser DevTools
- Server monitoring tools

## 🚀 Production Deployment Checklist

### Pre-Deployment
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Set proper file permissions
- [ ] Configure SSL certificate
- [ ] Set up backup system
- [ ] Configure monitoring
- [ ] Test all functionality in production

### Post-Deployment
- [ ] Verify all pages load correctly
- [ ] Test authentication flow
- [ ] Test file uploads
- [ ] Check email notifications
- [ ] Monitor error logs
- [ ] Performance testing

## 📝 Testing Report Template

```
Date: [Tanggal]
Tester: [Nama]
Environment: [Local/Staging/Production]

✅ Passed Tests:
- [List of passed tests]

❌ Failed Tests:
- [List of failed tests with details]

🐛 Bugs Found:
- [Description of bugs found]

🔧 Fixes Applied:
- [Description of fixes applied]

📊 Performance:
- Page load times
- Database query performance
- Memory usage

🎯 Recommendations:
- [Suggestions for improvement]

Next Testing Date: [Tanggal]
```

## 📞 Support Information

Jika menemukan bug atau masalah:
1. Dokumentasikan langkah reproduksi
2. Screenshot error message
3. Check browser console logs
4. Verify data di database
5. Report ke development team

---

**Note:** Testing adalah proses berkelanjutan. Lakukan testing reguler setiap ada perubahan signifikan di sistem.
