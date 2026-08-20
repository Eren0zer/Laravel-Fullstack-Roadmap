
# Gün 2 — Laravel Giriş

Laravel, modern ve güvenli web uygulamaları geliştirmek için kullanılan, ücretsiz ve açık kaynaklı bir PHP frameworküdür. Hazır yapı taşları sunarak sıfırdan kod yazma süresini kısaltır.

---

## 1. Proje Klasörleri

```text
app/        → Uygulamanın PHP kodları
bootstrap/  → Laravel'in ayağa kalkmasını sağlar
config/     → Uygulama ayarları
database/   → Migration, seeder, factory
public/     → Dış dünyaya açık klasör
resources/  → Blade, CSS, JS
routes/     → Hangi URL'nin ne yapacağını belirler
storage/    → Log, cache, upload gibi çalışma dosyaları
tests/      → Test kodları
vendor/     → Composer ile yüklenen paketler
```

---

## 2. Önemli Dosyalar ve Klasörler

### `routes/web.php`

Web üzerinden gelecek request'lerin hangi kod tarafından karşılanacağını tanımladığın temel route dosyasıdır.

```php
Route::get('/users', function () {
    //
});
```

---

### `app/Http/Controllers`

Gelen HTTP request ile uygulamanın geri kalanını koordine eder.

Katmanlar arasında koordinasyon sağlar.

```text
Request
   │
   ▼
Route
   │
   ▼
Controller
   │
   ├── Model
   ├── Service
   └── View
```

---

### `app/Models`

Laravel Model sınıflarının bulunduğu klasördür.

```text
app/
└── Models/
    └── User.php
```

---

### `resources/views`

Blade template'lerinin bulunduğu klasördür.

```text
resources/
└── views/
    ├── welcome.blade.php
    └── users.blade.php
```

---

### `database/migrations`

Database şemasını kod üzerinden tanımladığın migration dosyaları burada bulunur.

Örneğin:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->timestamps();
});
```

---

### `database/seeders`

Database'e başlangıç veya test verileri eklemek için kullanılan sınıfların bulunduğu klasördür.

```text
database/
└── seeders/
    └── DatabaseSeeder.php
```

---

### `config/`

Laravel'in configuration dosyaları burada bulunur.

```text
config/
├── app.php
├── database.php
├── cache.php
├── mail.php
└── services.php
```

---

### `.env`

Uygulamanın çalıştığı ortama özel ayarları ve secret'ları tutar.

Örneğin:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
```

### `.env` ve `config/` Farkı

**`.env`**

Ortama özel değerleri sağlar.

```env
DB_HOST=127.0.0.1
```

**`config/`**

Bu değerlerin uygulama içerisinde nasıl kullanılacağını yapılandırır.

```text
.env
 │
 │ Ortama özel değerler
 ▼
config/
 │
 │ Laravel yapılandırması
 ▼
Application
```

---

### `public/index.php`

Web server'dan Laravel uygulamasına gelen request'lerin entry point'idir.

Basit akış:

```text
Browser
   │
   │ HTTP Request
   ▼
public/index.php
   │
   ▼
Laravel
   │
   ▼
Route
   │
   ▼
Controller
```

---

## 2.1 Migration ve Seeder Farkı

### Migration

Database'in yapısını oluşturur.

```text
Migration
   │
   ├── Table oluşturur
   ├── Column ekler
   ├── Column değiştirir
   └── Relation tanımlar
```

Örneğin:

```text
users
-----
id
name
email
```

### Seeder

Database'in içini veriyle doldurur.

```text
Seeder
   │
   └── Database'e veri ekler
```

Örneğin:

```text
users
--------------------
1 | Ahmet
2 | Ayşe
3 | Mehmet
```

Kısaca:

| Yapı        | Görevi                      |
| ----------- | --------------------------- |
| `Migration` | Database yapısını oluşturur |
| `Seeder`    | Database'e veri ekler       |

---

## 3. Composer Nedir?

PHP'nin dependency / package manager'ıdır.

Projede ihtiyaç duyulan PHP paketlerini yüklemek ve yönetmek için kullanılır.

Örneğin:

```bash
composer install
```

```bash
composer require paket-adi
```

Laravel projesindeki Composer paketleri genellikle:

```text
vendor/
```

klasöründe bulunur.

---

## Gün 2 İlerleme

* [x] Laravel nedir?
* [x] Proje klasörleri
* [x] Önemli Laravel dosyaları
* [x] `.env` ve `config/`
* [x] Migration
* [x] Seeder
* [x] Composer
