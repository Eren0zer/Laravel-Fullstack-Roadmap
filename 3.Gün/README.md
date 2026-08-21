# Gün 3 — Middleware ve Routing

## 1. Middleware Nedir?

Middleware, HTTP request ile uygulamanın asıl işlemi arasına giren kontrol / işleme katmanıdır.

Örneğin kullanıcı:

```text
GET /admin
```

isteği attı.

Bu isteğin direkt olarak controller'a gitmesini istemeyebiliriz. İlk olarak kullanıcının giriş yapıp yapmadığını kontrol ederiz.

Bu kontrol kısmını middleware üstlenebilir.

```text
Request
   │
   ▼
Middleware
   │
   ├── Kontrol başarısız → Request'i durdur
   │
   ▼
Controller
```

Middleware'i basitçe **request ile controller arasındaki kontrol noktası** gibi düşünebiliriz.

---

### 1.1 Global Middleware

Uygulamaya gelen her request üzerinde çalışan middleware'dir.

Örneğin:

```text
GET /
GET /posts
POST /login
GET /admin
```

gibi istekler global middleware'den geçebilir.

Basit akış:

```text
Request
   │
   ▼
Global Middleware
   │
   ▼
Router
   │
   ▼
Controller
```

---

### 1.2 Route Middleware

Her route'a uygulanmaz. Belirli route'lara eklenir.

Örneğin:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');
```

Buradaki:

```php
->middleware('auth')
```

kısmından anlaşılacağı üzere `/dashboard` route'u `auth` middleware'inden geçer.

Akış:

```text
GET /dashboard
      │
      ▼
auth Middleware
      │
      ▼
Route
      │
      ▼
Controller / Closure
```

> **Önemli:** Middleware'in önemli özelliklerinden biri request'i durdurabilmesidir. Yani request controller'a ulaşmadan önce isteği kesebilir.

Örneğin kullanıcı giriş yapmamışsa:

```text
GET /dashboard
      │
      ▼
auth Middleware
      │
      ├── Giriş yapılmamış
      │
      ▼
Login sayfasına yönlendir
```

Controller bu durumda hiç çalışmaz.

---

### 1.3 Artisan ile Middleware Oluşturmak

Yeni bir middleware oluşturmak için:

```bash
php artisan make:middleware EnsureUserIsAdmin
```

Oluşturulan class genellikle:

```text
app/
└── Http/
    └── Middleware/
        └── EnsureUserIsAdmin.php
```

altına oluşturulur.

---

### 1.4 Birden Fazla Middleware

Bir route üzerinde birden fazla middleware kullanılabilir.

Örneğin:

```php
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware(['auth', 'admin']);
```

Akış:

```text
Request
   │
   ▼
auth
   │
   ▼
admin
   │
   ▼
AdminController
```

Burada request önce `auth`, ardından `admin` middleware'inden geçer.

Bu kontroller başarılı olursa `AdminController` çalışır.

---

## 2. Routing Nedir?

Route, belirli bir HTTP isteğini belirli bir koda bağlayan tanımdır.

Basit olarak:

```text
HTTP Request
     │
     ▼
   Route
     │
     ▼
Çalışacak Kod
```

Örneğin:

```text
GET /posts
```

isteğinin hangi kodu çalıştıracağını route belirler.

---
