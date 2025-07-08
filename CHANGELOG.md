# Changelog

Semua perubahan penting pada `apriansyahrs/import-excel` akan didokumentasikan dalam file ini.

Format ini berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan proyek ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Ditambahkan
- Release awal plugin Import Excel untuk Laravel Filament
- Dukungan multi-format (.xlsx, .xls, .xlsm, .csv)
- Background processing dengan Laravel Queue
- Multi-sheet handling dengan pemilihan dinamis
- Smart column mapping dengan deteksi header otomatis
- Robust error handling dengan tracking baris gagal
- Progress tracking dengan notifikasi real-time
- Model Import dan FailedImportRow yang custom
- Dokumentasi lengkap
- Test suite dengan Pest PHP

### Fitur
- `FullImportAction` untuk integrasi seamless dengan Filament
- Job `ImportExcel` untuk background processing
- Chunk size, max rows, dan header row yang dapat dikonfigurasi
- Dukungan untuk custom validation rules
- Multi-language support (Indonesia & Inggris)
- Arsitektur yang extensible untuk custom jobs dan notifications

## [1.0.0] - TBD

### Ditambahkan
- Stable release
- Fitur production-ready
- Dokumentasi lengkap
- Full test coverage

---

## Template Release Notes

```markdown
## [X.Y.Z] - YYYY-MM-DD

### Ditambahkan
- Fitur baru

### Diubah
- Perubahan pada fungsionalitas yang ada

### Deprecated
- Fitur yang akan segera dihapus

### Dihapus
- Fitur yang sudah dihapus

### Diperbaiki
- Perbaikan bug

### Keamanan
- Perbaikan keamanan
``` 