# Panduan Kontribusi

Terima kasih telah mempertimbangkan untuk berkontribusi pada Import Excel untuk Laravel Filament! Kami menyambut kontribusi dari komunitas.

## Setup Development

1. Fork repository
2. Clone fork Anda:
   ```bash
   git clone https://github.com/username-anda/import-excel.git
   cd import-excel
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Jalankan test untuk memastikan semuanya berfungsi:
   ```bash
   ./vendor/bin/pest
   ```

## Membuat Perubahan

1. Buat branch baru untuk fitur atau bug fix Anda:
   ```bash
   git checkout -b feature/nama-fitur-anda
   # atau
   git checkout -b fix/nama-bug-fix-anda
   ```

2. Buat perubahan mengikuti standar coding kami:
   - Ikuti standar coding PSR-12
   - Tulis test untuk fitur baru
   - Update dokumentasi jika diperlukan

3. Jalankan pengecekan kualitas kode:
   ```bash
   # Format kode
   ./vendor/bin/pint
   
   # Jalankan static analysis
   ./vendor/bin/phpstan analyse
   
   # Jalankan test
   ./vendor/bin/pest
   ```

## Submit Perubahan

1. Commit perubahan Anda dengan pesan yang deskriptif:
   ```bash
   git commit -m "feat: tambah dukungan untuk custom validation rules"
   # atau
   git commit -m "fix: perbaiki masalah memory dengan file besar"
   ```

2. Push ke fork Anda:
   ```bash
   git push origin nama-branch-anda
   ```

3. Buat Pull Request:
   - Berikan deskripsi yang jelas tentang perubahan Anda
   - Referensikan issue terkait jika ada
   - Sertakan screenshot jika berlaku

## Konvensi Commit

Kami menggunakan conventional commits:

- `feat:` - Fitur baru
- `fix:` - Perbaikan bug
- `docs:` - Perubahan dokumentasi
- `style:` - Perubahan style kode (formatting, dll.)
- `refactor:` - Refactoring kode
- `test:` - Menambah atau update test
- `chore:` - Task maintenance

## Testing

Semua perubahan kode harus menyertakan test yang sesuai:

- Unit test untuk method individual
- Feature test untuk fungsionalitas lengkap
- Integration test untuk komponen Filament

Jalankan test dengan:
```bash
./vendor/bin/pest
```

## Dokumentasi

Update dokumentasi ketika:
- Menambah fitur baru
- Mengubah fungsionalitas yang ada
- Memperbaiki error dokumentasi

File dokumentasi ada di direktori `docs/`.

## Code of Conduct

- Bersikap respectful dan inklusif
- Bantu orang lain belajar dan berkembang
- Berikan feedback yang konstruktif
- Fokus pada apa yang terbaik untuk komunitas

## Pertanyaan?

Jika Anda punya pertanyaan tentang kontribusi, silakan:
- Cek issue dan diskusi yang sudah ada
- Buat issue baru dengan label question
- Hubungi maintainer

Terima kasih telah berkontribusi! 🚀 