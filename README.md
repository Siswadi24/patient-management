# Patient Management System - PKU Muhammadiyah Wonosobo

Sistem manajemen pasien yang dibangun menggunakan Laravel 12 dengan Inertia.js dan Vue 3. Aplikasi ini menyediakan interface untuk mengelola data pasien dan sistem profil pengguna.

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 12
- **Frontend**: Vue 3 + TypeScript
- **Bridge**: Inertia.js
- **Database**: MySQL
- **UI Framework**: TailwindCSS + PrimeVue
- **Icons**: Lucide Vue
- **File Storage**: Local Storage (Public Disk)

## 📋 Fitur Utama

- ✅ Manajemen Data Pasien (CRUD)
- ✅ Sistem Autentikasi dengan Role-based Access
- ✅ Validasi Password Real-time dengan Indikator Visual
- ✅ Profile Management dengan Upload Photo
- ✅ Filter dan Pencarian Advanced
- ✅ Responsive Design (Mobile & Desktop)
- ✅ Real-time Alerts dan Notifications

## 🔐 Sistem Validasi Password

Aplikasi ini memiliki sistem validasi password yang ketat dengan indikator visual real-time:

### Syarat Password:
1. **Mengandung angka** (0-9)
2. **Mengandung huruf kapital** (A-Z) 
3. **Mengandung huruf non kapital** (a-z)
4. **Minimal password 7 huruf**

### Fitur Validasi:
- ✅ **Real-time validation** - Validasi langsung saat user mengetik
- ✅ **Visual indicators** - Icon check (✓) hijau jika memenuhi, silang (✗) merah jika belum
- ✅ **Dynamic border colors** - Border input berubah warna sesuai status validasi
- ✅ **Button disabled** - Tombol register tidak dapat diklik jika password belum memenuhi syarat
- ✅ **Password confirmation** - Validasi konfirmasi password dengan indikator visual

### Contoh Password Valid:
- `Password1` ✅
- `MyPass123` ✅
- `SecureP4ss` ✅

### Contoh Password Tidak Valid:
- `password` ❌ (tidak ada angka dan huruf kapital)
- `PASSWORD123` ❌ (tidak ada huruf kecil)
- `Pass1` ❌ (kurang dari 7 karakter)

## 🚀 Langkah-langkah Instalasi

### Prerequisites
Pastikan sistem Anda sudah terinstall:
- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM atau Yarn
- MySQL Database

### 1. Clone Repository
```bash
git clone https://github.com/username/patient-management-system.git
cd patient-management-system
```

### 2. Install Dependencies Backend
```bash
composer install
```

### 3. Install Dependencies Frontend
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
```bash
# Buat database MySQL
# mysql -u root -p
# CREATE DATABASE pku_patient_management;

# Jalankan migrasi database
php artisan migrate

# Seed data awal(Digunakan Untuk Seed Roles)
php artisan db:seed
```

### 6. Storage Link
```bash
# Buat symbolic link untuk storage
php artisan storage:link
```

### 7. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

## ⚙️ Konfigurasi Environment (.env)

Berikut adalah konfigurasi yang **WAJIB** diperbaiki di file `.env`:

### ⚠️ Perbaikan untuk .env Anda:

```env
# Application Settings - PERBAIKAN: URL harus lengkap dengan port
APP_NAME='PKU Patient Management System'
APP_ENV=local
APP_KEY=base64:2XlpzNnDmAbCBhN8nEGiEYI3vM/dnHgyFgGL7GoSjT4=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# PERBAIKAN: Filesystem harus public untuk upload file
FILESYSTEM_DISK=public

# PERBAIKAN: Session driver sebaiknya file untuk development
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# PERBAIKAN: Cache sebaiknya file untuk development
CACHE_STORE=file

# PERBAIKAN: Queue sebaiknya sync untuk development
QUEUE_CONNECTION=sync

# Database Configuration - Sudah benar
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pku_patient_management
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration - Sudah benar
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=aa9dd29d789b07
MAIL_PASSWORD=25594e5a8ce241
MAIL_FROM_ADDRESS="perdanaputra2016@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

# VITE Configuration - Sudah benar
VITE_APP_NAME="${APP_NAME}"
```

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pku_patient_management
DB_USERNAME=root
DB_PASSWORD=
```

### File Storage Configuration
```env
# PENTING: Ubah ke public untuk upload file
FILESYSTEM_DISK=public
```

### Session & Cache Configuration
```env
# Untuk development, gunakan file driver
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Application URL
```env
# PENTING: Harus lengkap dengan port
APP_URL=http://127.0.0.1:8000
```

### 📧 Konfigurasi Email (Mailtrap)

Untuk fitur notifikasi email dan verifikasi, aplikasi menggunakan **Mailtrap** sebagai SMTP testing service. Konfigurasi email yang sudah disediakan:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=aa9dd29d789b07
MAIL_PASSWORD=25594e5a8ce241
MAIL_FROM_ADDRESS="perdanaputra2016@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> **📝 Note Penting**: 
> Notifikasi email menggunakan **Mailtrap** untuk testing. Jika Anda mengalami kesulitan dengan konfigurasi email atau membutuhkan akses OTP untuk login, silakan hubungi developer di email **danaperdanaputra32@gmail.com** dan saya akan memberikan informasi OTP yang diperlukan.

## 🏃‍♂️ Cara Menjalankan Aplikasi

### Development Mode

#### 1. Start Laravel Server
```bash
php artisan serve

# Atau bisa langsung dengan
composer run dev
```
Server akan berjalan di: `http://127.0.0.1:8000`

#### 2. Start Vite Development Server (Terminal Baru)
```bash
npm run dev

# Atau bisa langsung dengan
composer run dev
```
Vite akan berjalan di: `http://127.0.0.1:5173`

#### 3. Akses Aplikasi
Buka browser dan kunjungi: `http://127.0.0.1:8000`

### Production Mode

#### 1. Build Assets untuk Production
```bash
npm run build
```

#### 2. Setup Web Server
Konfigurasikan web server (Apache/Nginx) untuk mengarah ke folder `public/`

#### 3. Optimasi Laravel
```bash
# Clear dan cache konfigurasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Untuk production, set APP_ENV=production di .env
```

## 🔧 Commands Berguna

### Development
```bash
# Install dependencies
composer install && npm install

# Jalankan migrasi database
php artisan migrate

# Reset database dengan seeder
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Buat storage link (penting untuk upload file)
php artisan storage:link

# Generate IDE Helper (opsional)
php artisan ide-helper:generate
```

### Production
```bash
# Optimasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

## 🐛 Troubleshooting

### Error: "Storage link not found"
```bash
php artisan storage:link
```

### Error: "Mix manifest not found" atau Vite Error
```bash
npm run dev
# atau untuk production
npm run build
```

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error Database Connection
- Pastikan MySQL service berjalan
- Periksa konfigurasi database di `.env`
- Pastikan database `pku_patient_management` sudah dibuat

### Error Permission (Linux/Mac)
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Error Upload File "404 Not Found"
```bash
# Pastikan storage link sudah dibuat
php artisan storage:link

# Periksa .env FILESYSTEM_DISK=public
```

### Error Email/SMTP
- Pastikan konfigurasi email Mailtrap sudah benar
- Jika masih bermasalah, hubungi **danaperdanaputra32@gmail.com** untuk bantuan OTP

## 📁 Struktur Project

```
patient-management-system/
├── app/
│   ├── Http/Controllers/          # Controllers
│   ├── Models/                    # Eloquent Models
│   └── Http/Middleware/           # Custom Middleware
├── resources/
│   ├── js/
│   │   ├── components/            # Vue Components
│   │   ├── pages/                 # Inertia Pages
│   │   ├── layouts/              # Layout Components
│   │   └── types/                # TypeScript Types
│   └── views/                    # Blade Templates
├── routes/
│   ├── web.php                   # Web Routes
│   ├── auth.php                  # Auth Routes
│   └── settings.php              # Settings Routes
├── database/
│   ├── migrations/               # Database Migrations
│   └── seeders/                  # Database Seeders
├── storage/
│   └── app/public/               # File uploads
└── public/                       # Public Assets
    └── storage/                  # Symlink ke storage/app/public
```

## 📝 API Endpoints

### Patient Management
- `GET /user/patient` - List patients dengan filter
- `POST /user/patient/store-patient` - Create patient
- `PUT /user/patient/update-patient/{id}` - Update patient
- `DELETE /user/patient/delete-patient/{id}` - Delete patient

### Profile Management
- `GET /settings/profile` - Profile settings page
- `PATCH /settings/profile` - Update profile
- `POST /settings/profile/avatar` - Upload profile photo

## 🤝 Contributing

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan LICENSE IBOYY.

## 📞 Support

Jika mengalami kendala, silakan:
1. Cek bagian Troubleshooting di atas
2. Buat issue di GitHub repository
3. **Contact Developer**: danaperdanaputra32@gmail.com 
   - Untuk bantuan konfigurasi email/SMTP
   - Untuk mendapatkan OTP login
   - Untuk support teknis lainnya

---

**Dibuat dengan ❤️ untuk PKU Muhammadiyah Wonosobo**
