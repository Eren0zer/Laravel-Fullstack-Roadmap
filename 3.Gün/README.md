# Gün 3 — Middleware, Routing, Controller ve Blade

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
   ▼
Controller
```

Temel olarak iki kullanım şeklini görebiliriz:

```text
Global Middleware
Route Middleware
```

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

```text
Request
   │
   ▼
Global Middleware
   │
   ▼
Route
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

kısmından da anlaşılacağı üzere request `auth` middleware'inden geçer.

```text
GET /dashboard
      │
      ▼
auth Middleware
      │
      ▼
Route işlemi
```

> **Önemli:** Middleware'in önemli özelliklerinden biri request'i durdurabilmesidir. Request controller'a ulaşmadan isteği kesebilir.

Örneğin kullanıcı giriş yapmamışsa:

```text
Request
   │
   ▼
auth
   │
   ├── Giriş yapılmış → Devam et
   │
   └── Giriş yapılmamış → İsteği durdur / yönlendir
```

---

### 1.3 Artisan ile Middleware Oluşturmak

Yeni bir middleware oluşturmak için:

```bash
php artisan make:middleware EnsureUserIsAdmin
```

Oluşturulan class genellikle:

```text
app/Http/Middleware/
```

altına oluşturulur.

Örneğin:

```text
app/
└── Http/
    └── Middleware/
        └── EnsureUserIsAdmin.php
```

---

### 1.4 Birden Fazla Middleware

Bir route'a birden fazla middleware eklenebilir.

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

Yani request önce `auth`, ardından `admin` middleware'inden geçer.

---

# 2. Routing Nedir?

Route, belirli bir HTTP isteğini belirli bir koda bağlayan tanımdır.

Örneğin:

```text
GET /posts
```

isteği geldiğinde Laravel'e:

> `/posts` adresine `GET` isteği gelirse ne yapacağım?

sorusunun cevabını route ile veririz.

Basit mantık:

```text
HTTP Request
     │
     ▼
    Route
     │
     ▼
Çalışacak Kod
```

---

## 2.1 `Route::get()`

Bir sayfayı veya veriyi okumak / göstermek istediğimizde kullanılır.

Örneğin:

```php
Route::get('/posts', function () {
    return 'Postlar';
});
```

Burada:

```text
GET /posts
     │
     ▼
Route çalışır
     │
     ▼
Postlar
```

---

## 2.2 `Route::post()`

Genellikle yeni bir veri oluşturmak için kullanılır.

Örneğin:

```php
Route::post('/posts', function () {
    return 'Post oluşturuldu';
});
```

---

## 2.3 PUT ve PATCH

İkisi de mevcut bir veriyi güncellemek için kullanılır.

```text
PUT   → Kaynağın tamamını güncelle
PATCH → Kaynağın bir kısmını güncelle
```

Örneğin:

```php
Route::put('/posts/{id}', function ($id) {
    return "Post {$id} güncellendi";
});
```

```php
Route::patch('/posts/{id}', function ($id) {
    return "Post {$id} güncellendi";
});
```

Buradaki:

```text
{id}
```

route parametresidir.

Örneğin:

```text
PUT /posts/5
```

isteğinde:

```text
id = 5
```

olur.

---

## 2.4 `Route::delete()`

Bir kaynağı silmek için kullanılır.

```php
Route::delete('/posts/{id}', function ($id) {
    return "Post {$id} silindi";
});
```

Örneğin:

```text
DELETE /posts/5
```

isteği `id = 5` olan post üzerinde işlem yapabilir.

---

## 2.5 CRUD ile Bağlantısı

Bunu özellikle HTTP metotlarıyla eşleştirmek önemli:

| İşlem         | HTTP          | Laravel                           |
| ------------- | ------------- | --------------------------------- |
| Listele / Oku | `GET`         | `Route::get()`                    |
| Oluştur       | `POST`        | `Route::post()`                   |
| Güncelle      | `PUT / PATCH` | `Route::put()` / `Route::patch()` |
| Sil           | `DELETE`      | `Route::delete()`                 |

Kısaca:

```text
CREATE → POST
READ   → GET
UPDATE → PUT / PATCH
DELETE → DELETE
```

---

# 3. Controller Nedir?

Route hangi kodun çalışacağını seçer, Controller ise request'in nasıl işleneceğini koordine eder.

Örneğin:

```text
GET /posts
```

şeklinde bir request geldiğini düşünelim.

Route:

```php
Route::get('/posts', [PostController::class, 'index']);
```

Laravel burada şunu söylüyor:

```text
/posts adresine GET gelirse
          │
          ▼
PostController'a git
          │
          ▼
index() metodunu çalıştır
```

Yani:

```text
Request
   │
   ▼
Route
   │
   ▼
Controller
   │
   ▼
Controller Method
```

Route **nereye gidileceğini**, Controller ise **isteğin nasıl işleneceğini** belirler.

---

# 4. Blade

Controller veriyi hazırlar, Blade bu veriyi HTML içerisinde kullanıcıya gösterir.

Blade, Laravel'in template engine'idir.

Normal PHP ile HTML'i şöyle yazabilirdik:

```php
<h1><?php echo $name; ?></h1>
```

Blade ile:

```blade
<h1>{{ $name }}</h1>
```

Blade, bu tarz işlemleri daha okunabilir şekilde yapma imkânı verir.

> **Önemli:** `{{ }}` sadece veriyi yazdırmakla kalmaz, varsayılan olarak HTML'i **escape** eder.

---

## 4.1 `@if`

Normal PHP:

```php
<?php if ($user->is_admin): ?>
    <p>Admin</p>
<?php endif; ?>
```

Blade:

```blade
@if ($user->is_admin)
    <p>Admin</p>
@endif
```

Yani Blade, PHP içerisinde sık kullandığımız kontrol yapılarını daha okunabilir hale getirir.

---

## 4.2 Layout Mantığı: `@extends`, `@section`, `@yield`

Blade içerisinde ortak sayfa yapıları oluşturabiliriz.

Örneğin bütün sayfalarda aynı:

```text
Navbar
Footer
CSS
Genel HTML yapısı
```

olabilir.

Bunları her sayfada tekrar yazmak yerine bir layout oluşturabiliriz.

### `@extends`

Hangi layout'u kullanacağını söyler.

```blade
@extends('layouts.app')
```

---

### `@section`

Sayfanın belirli içeriğini tanımlar.

```blade
@section('content')

    <h1>Postlar</h1>

@endsection
```

---

### `@yield`

Layout içerisinde içeriğin geleceği yeri belirtir.

```blade
@yield('content')
```

Basit olarak:

```text
@extends → Hangi layout?

@section → Bu sayfanın içeriği ne?

@yield   → O içerik layout'un neresine gelecek?
```

Akış:

```text
layouts/app.blade.php
        │
        │ @yield('content')
        ▼
İçeriğin geleceği alan
        ▲
        │
@section('content')
        │
posts/index.blade.php
```

---

## 4.3 Önemli Kısım: `{{ }}` ve `{!! !!}`

İki kullanım vardır:

```blade
{{ $variable }}
```

ve:

```blade
{!! $variable !!}
```

Bunlar aynı değildir.

### `{{ $variable }}`

Laravel bunu escape eder.

Örneğin kullanıcı şunu gönderirse:

```html
<script>alert('hack')</script>
```

ve Blade'de:

```blade
{{ $comment }}
```

yazarsak, browser bunu script olarak çalıştırmak yerine metin olarak gösterir.

Kabaca:

```html
&lt;script&gt;alert('hack')&lt;/script&gt;
```

haline dönüştürülür.

Yani kullanıcı ekranda:

```text
<script>alert('hack')</script>
```

yazısını görür fakat script çalışmaz.

Bu, **XSS saldırılarına karşı önemli bir korumadır.**

---

### `{!! $variable !!}`

Bu kullanım veriyi escape etmeden, raw HTML olarak yazdırır.

```blade
{!! $variable !!}
```

Örneğin:

```php
$variable = '<strong>Merhaba</strong>';
```

kullanılırsa browser bunu gerçekten HTML olarak yorumlayabilir:

```html
<strong>Merhaba</strong>
```

Bu yüzden kullanıcıdan gelen veya güvenmediğimiz verilerde:

```blade
{!! !!}
```

kullanmak tehlikeli olabilir.

Kısaca:

| Kullanım            | Davranış           | Güvenlik              |
| ------------------- | ------------------ | --------------------- |
| `{{ $variable }}`   | HTML'i escape eder | Daha güvenli          |
| `{!! $variable !!}` | Raw HTML basar     | Dikkatli kullanılmalı |

Genel olarak:

```blade
{{ $variable }}
```

kullanmak daha güvenlidir.

---
