
# SISTEM PEMINJAMAN BUKU PERPUSTAKAAN (LANTERA)

## Cara integrasi repository ke laptop masing-masing:

1. install git di laptop masing masing
2. jika sudah jalankan command berikut di git (sesuaikan dimana lokasi file ingin disimpan)

```bash
  git clone https://github.com/ArcNasss/lantera.git
```
```bash
  cd lantera
```
```bash
  composer install
  npm install
```
(kemudian lakukan konfigurasi tailwind untuk laravel 12)

```bash
  cp .env.example .env
```
3. Konfigurasi file .env , sesuaikan dengan nama database masing masing

4. Run project dengan command

```bash
  php artisan serve
  npm run dev
```



-created with 😝 by Nasril

