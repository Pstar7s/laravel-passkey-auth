# Laravel Passkey Authentication

![Laravel](https://img.shields.io/badge/Laravel-10.x-red)
![WebAuthn](https://img.shields.io/badge/WebAuthn-Enabled-blue)
![License](https://img.shields.io/badge/License-MIT-green)

Implementasi modern **Passkey Authentication** menggunakan Laravel 10 dan WebAuthn (FIDO2). Project ini mendemonstrasikan cara membuat sistem authentication tanpa password yang lebih aman dan user-friendly.

## ⚠️ Status Project

**Passkey Registration**: ✅ Berfungsi sempurna
- User dapat register dan membuat passkey
- Credential tersimpan ke database dengan benar
- Challenge generation working
- Browser biometric dialog muncul

**Passkey Login**: ⚠️ Dalam development
- Ada bug di package Laragear/WebAuthn v3.1.1 dimana `verify` flag tidak di-set dengan benar
- Credential dapat ditemukan tapi validation gagal
- Workaround sedang dikembangkan

## 🌟 Fitur

- ✅ **Passwordless Authentication** - Login tanpa password menggunakan biometric atau PIN device
- ✅ **WebAuthn/FIDO2** - Standar keamanan modern yang didukung semua browser utama
- ✅ **Biometric Support** - Face ID, Touch ID, Windows Hello, fingerprint scanner
- ✅ **Anti-Phishing** - Credential tidak pernah meninggalkan device Anda
- ✅ **User-Friendly** - Interface yang simple dan mudah digunakan
- ✅ **Laravel 10** - Framework PHP modern dan powerful

## 🛠️ Tech Stack

- **Backend**: Laravel 10
- **Package**: [Laragear/WebAuthn](https://github.com/Laragear/WebAuthn)
- **Frontend**: Tailwind CSS, SimpleWebAuthn Browser Library
- **Database**: SQLite (bisa diganti MySQL/PostgreSQL)

## 📋 Requirements

- PHP >= 8.1
- Composer
- HTTPS atau localhost (WebAuthn requirement)
- Browser yang support WebAuthn (Chrome, Firefox, Safari, Edge terbaru)

## 🚀 Instalasi

Project ini sudah siap digunakan! Berikut langkah-langkah untuk menjalankannya:

### 1. Install Dependencies (Sudah dilakukan)
```bash
composer install
```

### 2. Konfigurasi Environment (Sudah dikonfigurasi)
Database sudah diatur menggunakan SQLite dan migration sudah dijalankan.

### 3. Generate Application Key (Sudah dilakukan)
```bash
php artisan key:generate
```

### 4. Jalankan Development Server
```bash
php artisan serve
```

Buka browser dan akses: `http://localhost:8000`

## 📖 Cara Penggunaan

### Registrasi User Baru
1. Klik "Mulai Register" atau akses `/register`
2. Masukkan nama dan email
3. Klik "Register & Setup Passkey"
4. Browser akan meminta Anda untuk membuat passkey menggunakan:
   - Face ID / Touch ID (Mac/iPhone)
   - Windows Hello (Windows)
   - Fingerprint / PIN (Android)
   - Security Key (Yubikey, dll)
5. Setelah berhasil, Anda akan otomatis login dan redirect ke dashboard

### Login dengan Passkey
1. Klik "Login" atau akses `/login`
2. Masukkan email yang sudah terdaftar
3. Klik "Login dengan Passkey"
4. Browser akan meminta verifikasi biometric/PIN
5. Setelah berhasil, Anda akan login dan redirect ke dashboard

## 🏗️ Struktur Project

```
app/
├── Http/Controllers/
│   ├── PasskeyAuthController.php      # Main authentication controller
│   └── WebAuthn/                      # WebAuthn controllers (from package)
│       ├── WebAuthnRegisterController.php
│       └── WebAuthnLoginController.php
└── Models/
    └── User.php                        # User model with WebAuthnAuthentication trait

resources/views/
├── layouts/
│   └── app.blade.php                  # Main layout
├── auth/
│   ├── register.blade.php             # Registration page with passkey setup
│   └── login.blade.php                # Login page with passkey
├── dashboard.blade.php                # Dashboard after login
└── welcome.blade.php                  # Landing page

routes/
└── web.php                            # Web routes

database/
├── database.sqlite                    # SQLite database
└── migrations/
    └── xxxx_create_webauthn_credentials.php  # WebAuthn credentials table
```

## 🔐 Cara Kerja WebAuthn

### Registration Flow:
1. User mengisi form (nama + email)
2. Server membuat user baru di database
3. Server generate "challenge" untuk WebAuthn
4. Browser meminta user membuat credential (biometric/PIN)
5. Public key disimpan di server, private key tetap di device user

### Login Flow:
1. User memasukkan email
2. Server generate "challenge" untuk authentication
3. Browser meminta user verifikasi dengan credential yang sama
4. Server memverifikasi signature dan login user

## 📱 Browser Support

| Browser | Desktop | Mobile |
|---------|---------|--------|
| Chrome  | ✅ | ✅ |
| Firefox | ✅ | ✅ |
| Safari  | ✅ | ✅ |
| Edge    | ✅ | ✅ |

## 🔧 Konfigurasi

File konfigurasi WebAuthn tersimpan di `config/webauthn.php`. Anda bisa customize:
- Relying Party Name
- User verification requirements
- Attestation conveyance
- Timeout settings
- Dan lain-lain

## 🌐 Production Deployment

Untuk production, pastikan:

1. **Gunakan HTTPS** - WebAuthn require secure context
2. **Update .env**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```
3. **Gunakan database production** (MySQL/PostgreSQL):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=your-host
   DB_DATABASE=your-database
   DB_USERNAME=your-username
   DB_PASSWORD=your-password
   ```
4. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

## 🤝 Contributing

Contributions, issues, dan feature requests sangat diterima!

## 📝 License

Project ini menggunakan MIT License.

## 👨‍💻 Author

Dibuat dengan ❤️ untuk belajar WebAuthn dan Laravel

## 📚 Resources

- [WebAuthn Guide](https://webauthn.guide/)
- [Laragear/WebAuthn Documentation](https://laragear.github.io/WebAuthn/)
- [FIDO Alliance](https://fidoalliance.org/)
- [MDN WebAuthn API](https://developer.mozilla.org/en-US/docs/Web/API/Web_Authentication_API)

## 🎯 Next Steps

Beberapa improvement yang bisa ditambahkan:
- [ ] Multiple passkeys per user
- [ ] Passkey management (list, rename, delete)
- [ ] Fallback authentication method
- [ ] Email verification
- [ ] Rate limiting
- [ ] Audit logging

---

**Happy Coding! 🚀**
