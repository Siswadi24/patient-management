# Patient Management System - PKU Muhammadiyah Wonosobo

Sistem manajemen pasien yang dibangun menggunakan Laravel 11 dengan Inertia.js dan Vue 3. Aplikasi ini menyediakan interface untuk mengelola data pasien, transaksi finansial, dan sistem profil pengguna.

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Frontend**: Vue 3 + TypeScript
- **Bridge**: Inertia.js
- **Database**: MySQL
- **UI Framework**: TailwindCSS + PrimeVue
- **Icons**: Lucide Vue
- **File Storage**: Local Storage (Public Disk)

## 📋 Fitur Utama

- ✅ Manajemen Data Pasien (CRUD)
- ✅ Sistem Autentikasi dengan Role-based Access
- ✅ Manajemen Transaksi Finansial
- ✅ Kategori Transaksi
- ✅ Profile Management dengan Upload Photo
- ✅ Dashboard Analytics
- ✅ Filter dan Pencarian Advanced
- ✅ Responsive Design (Mobile & Desktop)
- ✅ Real-time Alerts dan Notifications

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
# atau
yarn install
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
# Jalankan migrasi database
php artisan migrate

# (Opsional) Seed data awal
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

Berikut adalah konfigurasi yang **WAJIB** diatur di file `.env`:

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=patient_management
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### Application Settings
```env
APP_NAME="Patient Management System"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

### Mail Configuration (untuk fitur verifikasi email)
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### File Storage
```env
FILESYSTEM_DISK=local
```

### Session & Cache
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### External API (untuk data pasien)
```env
PATIENT_API_URL=https://mockapi.pkuwsb.id/api
PATIENT_API_USERNAME=admin
PATIENT_API_PASSWORD=secret
```

## 🏃‍♂️ Cara Menjalankan Aplikasi

### Development Mode

#### 1. Start Laravel Server
```bash
php artisan serve
```
Server akan berjalan di: `http://127.0.0.1:8000`

#### 2. Start Vite Development Server (Terminal Baru)
```bash
npm run dev
```
Vite akan berjalan di: `http://127.0.0.1:5173`

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

## 👤 Akun Default

Setelah menjalankan `php artisan db:seed`, akun berikut akan tersedia:

### Super Admin
- **Email**: admin@example.com
- **Password**: password
- **Role**: Admin

### User
- **Email**: user@example.com
- **Password**: password
- **Role**: User

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
└── public/                       # Public Assets
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

### Error: "Mix manifest not found"
```bash
npm run dev
# atau
npm run build
```

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error Database Connection
- Pastikan MySQL service berjalan
- Periksa konfigurasi database di `.env`
- Pastikan database sudah dibuat

### Error Permission (Linux/Mac)
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 📝 API Endpoints

### Patient Management
- `GET /user/patient` - List patients dengan filter
- `POST /user/patient/store-patient` - Create patient
- `PUT /user/patient/update-patient/{id}` - Update patient
- `DELETE /user/patient/delete-patient/{id}` - Delete patient

### Transaction Management
- `GET /user/transactions` - List transactions
- `POST /user/transactions` - Create transaction
- `PUT /user/transactions/{id}` - Update transaction
- `DELETE /user/transactions/{id}` - Delete transaction

### Categories
- `GET /user/categories` - List categories
- `POST /user/categories` - Create category
- `PUT /user/categories/{id}` - Update category
- `DELETE /user/categories/{id}` - Delete category

## 🤝 Contributing

1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan [MIT License](LICENSE).

## 📞 Support

Jika mengalami kendala, silakan:
1. Cek bagian Troubleshooting di atas
2. Buat issue di GitHub repository
3. Contact: developer@pkumuhammadiyahwonosobo.id

---

**Dibuat dengan ❤️ untuk PKU Muhammadiyah Wonosobo**
