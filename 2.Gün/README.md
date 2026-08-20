
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
### 3.1 `composer.json`

Projenin Composer yapılandırmasını tutar.

Örneğin:

```json
{
    "require": {
        "php": "^8.3",
        "laravel/framework": "^13.0"
    }
}
```

### `require`

Uygulamanın normal çalışması için gereken paketleri belirtir.

```json
"require": {
    "php": "^8.3",
    "laravel/framework": "^13.0"
}
```

### `require-dev`

Genellikle geliştirme ve test sırasında gereken paketleri belirtir.

```json
"require-dev": {
}
```

`composer.json` genellikle doğrudan kesin sürümü değil, hangi sürümlerin kullanılabileceğini belirleyen sürüm kurallarını tutar.

Örneğin:

```text
"laravel/framework": "^13.0"
```

---

### 3.2 `composer.lock`

Dependency'lerin kesin olarak kullanılan sürümlerini tutar.

Örneğin `composer.json`:

```text
laravel/framework: ^13.0
```

şeklinde izin verilen sürüm aralığını belirtirken, `composer.lock` çözülen kesin sürümü tutar.

Basit olarak:

```text
composer.json
     │
     │ Kullanılabilecek sürümleri belirtir
     ▼
composer.lock
     │
     │ Çözülmüş kesin sürümleri tutar
     ▼
vendor/
```

Kısaca:

```text
composer.json → hangi sürümler kullanılabilir?
composer.lock → hangi sürümler gerçekten kullanılıyor?
```

---

### 3.3 `composer install`

Eğer `composer.lock` varsa, lock dosyasında belirlenmiş sürümleri kurar.

```text
composer.lock
      │
      ▼
composer install
      │
      ▼
Belirlenmiş dependency sürümlerini kur
```

Eğer `composer.lock` yoksa, `composer.json` içerisindeki kurallara göre dependency'leri çözer ve bir `composer.lock` dosyası oluşturur.

---

### 3.4 `composer update`

Dependency sürümlerini `composer.json` içerisindeki kurallara göre yeniden çözer.

Sonrasında `composer.lock` dosyasını günceller.

```text
composer.json
      │
      ▼
composer update
      │
      ▼
Uygun yeni sürümleri çöz
      │
      ▼
composer.lock'u güncelle
```

---

### 3.5 `composer install` ile `composer update` Farkı

**`composer install`**

Varsa `composer.lock` dosyasındaki belirlenmiş sürümleri indirir.

```text
composer.lock
      │
      ▼
composer install
      │
      ▼
Aynı sürümleri kur
```

**`composer update`**

`composer.json` içerisindeki kurallara göre dependency sürümlerini yeniden çözer ve `composer.lock` dosyasını günceller.

```text
composer.json
      │
      ▼
composer update
      │
      ▼
Yeni uygun sürümleri çöz
      │
      ▼
composer.lock
```

Kısaca:

| Komut              | Görevi                                              |
| ------------------ | --------------------------------------------------- |
| `composer install` | Lock dosyasındaki belirlenmiş sürümleri kurar       |
| `composer update`  | Sürümleri yeniden çözer ve lock dosyasını günceller |

---

### 3.6 `vendor/`

Composer'ın indirdiği dependency'lerin bulunduğu klasördür.

Elle değiştirmezsin. Çünkü bunlar dış bağımlılıklardır ve başka Composer işlemleri yapıldığında yaptığın değişiklikler kaybolabilir.

```text
vendor/
├── laravel/
├── symfony/
├── psr/
└── ...
```

---

### 3.7 Neden `vendor/` Git'e Gönderilmiyor?

Çünkü dependency bilgisi zaten:

```text
composer.json
composer.lock
```

içerisinde bulunur.

Projeyi Git'ten indiren kişi:

```bash
composer install
```

komutunu çalıştırarak dependency'leri tekrar kurabilir.

```text
Git Repository
     │
     ├── composer.json
     └── composer.lock
              │
              ▼
       composer install
              │
              ▼
           vendor/
```

---

## 4. Artisan Nedir?

Laravel'in kendi CLI aracıdır.

Laravel projesinde bazı işleri terminalden yapmak için:

```bash
php artisan ...
```

komutlarını kullanırsın.

---

### 4.1 `php artisan serve`

Laravel uygulamasını geliştirme ortamında ayağa kaldırmak için kullanılabilir.

```bash
php artisan serve
```

Geliştirme ortamında uygulamayı görüntülemek ve test etmek için kullanılır.

---

### 4.2 `php artisan route:list`

Uygulamadaki route'ları gösterir.

Örneğin `web.php` içerisinde:

```php
Route::get('/posts', [PostController::class, 'index'])
    ->name('posts.index');
```

Route listesinde buna benzer bir çıktı görülebilir:

```text
GET|HEAD   posts   posts.index   PostController@index
```

Burada:

```text
HTTP Method → GET
URL         → /posts
Route Name  → posts.index
Controller  → PostController@index
```

---

### 4.3 `make:` Komutları

Dosya oluşturma komutlarıdır.

Örneğin:

```bash
php artisan make:controller PostController
```

Laravel:

```text
app/
└── Http/
    └── Controllers/
        └── PostController.php
```

dosyasını oluşturur.

Başka örnekler:

```bash
php artisan make:model Post

php artisan make:migration create_posts_table
```

---

### 4.4 Migration Oluşturmak

Migration, Database şemasını kod üzerinden tanımlar.

```bash
php artisan make:migration create_posts_table
```

Bu komut migration dosyasını oluşturur.

```text
php artisan make:migration create_posts_table
                 │
                 ▼
database/migrations/
                 │
                 ▼
..._create_posts_table.php
```

---

### 4.5 `php artisan migrate`

Migration dosyası oluşturmakla migration'ı çalıştırmak farklı şeylerdir.

```bash
php artisan make:migration create_posts_table
```

Sadece migration dosyasını oluşturur.

Database henüz değişmemiştir.

```bash
php artisan migrate
```

Henüz çalıştırılmamış migration'ları database'e uygular.

```text
make:migration
      │
      ▼
Migration dosyası oluştur
      │
      ▼
Sen migration'ı yazarsın
      │
      ▼
php artisan migrate
      │
      ▼
Database şeması değişir
```

---

### 4.6 `php artisan migrate:rollback`

Son migration batch'inde çalıştırılmış migration'ları geri alır.

```bash
php artisan migrate:rollback
```

Basit akış:

```text
php artisan migrate
        │
        ▼
Database şeması değişir
        │
        ▼
php artisan migrate:rollback
        │
        ▼
Son migration batch'i geri alınır
```

---

### Composer ve Artisan Özeti

```text
                    COMPOSER
                       │
                       ▼
              PHP Dependencies
                       │
                       ▼
                    Laravel
                       │
                       │
                    ARTISAN
                       │
        ┌──────────────┼─────────────┐
        ▼              ▼             ▼
   Code üret        DB yönet      Bilgi / işlem
        │              │             │
 make:model        migrate       route:list
 make:controller   rollback      serve
 make:migration
```

**Composer** PHP dependency'lerini yönetir.

**Artisan** Laravel uygulamasıyla ilgili işlemleri terminal üzerinden yapmanı sağlar.

---

## 5. Request Lifecycle

Browser'dan gelen bir request'in Laravel içerisindeki genel akışı:

```text
Browser
   │
   │ GET /posts/5
   ▼
Web Server
   │
   ▼
public/index.php
   │
   ▼
Composer Autoload
   │
   ▼
bootstrap/app.php
   │
   ▼
Laravel Application
   │
   ▼
Middleware
   │
   ▼
Router
   │
   │ Uygun route'u bulur
   ▼
Route Middleware
   │
   ▼
Controller
   │
   ▼
Model / Service
   │
   ▼
Database
   │
   ▼
Controller
   │
   ├──────────────┐
   ▼              ▼
Blade           JSON
   │              │
   └──────┬───────┘
          ▼
       Response
          │
          ▼
Middleware'lerden geri
          │
          ▼
       Browser
```

### Akışı Kısaca Okursak

```text
Browser request gönderir
        ↓
Web Server request'i public/index.php'ye yönlendirir
        ↓
Composer Autoload yüklenir
        ↓
Laravel uygulaması bootstrap/app.php üzerinden başlatılır
        ↓
Request middleware'lerden geçer
        ↓
Router uygun route'u bulur
        ↓
Route middleware'leri çalışır
        ↓
Controller çalışır
        ↓
Gerekirse Model / Service / Database ile işlem yapılır
        ↓
Blade veya JSON Response oluşturulur
        ↓
Response middleware zincirinden geri geçer
        ↓
Browser'a gönderilir
```

---

## Gün 2 İlerleme

* [x] Laravel nedir?
* [x] Proje klasörleri
* [x] Önemli Laravel dosyaları
* [x] `.env` ve `config/`
* [x] Migration
* [x] Seeder
* [x] Composer
* [x] `composer.json`
* [x] `composer.lock`
* [x] `composer install`
* [x] `composer update`
* [x] `vendor/`
* [x] Artisan
* [x] `route:list`
* [x] `make:` komutları
* [x] Migration komutları
* [x] Request Lifecycle

