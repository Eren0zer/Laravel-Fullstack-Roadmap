# Gün 3 — Laravel Temel Yapıları

## 1. Middleware Nedir?

Middleware, HTTP request ile uygulamanın asıl işlemi arasına giren kontrol / işleme katmanıdır.

Örneğin kullanıcı:

```text
GET /admin
```

isteği attı.

Direkt olarak controller'a gitmesini istemeyebiliriz. İlk olarak kullanıcı giriş yapmış mı kontrol ederiz. Bu kontrol kısmını middleware üstlenebilir.

```text
Request
   │
   ▼
Middleware
   │
   ▼
Controller
```

İki tip middleware vardır:

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

Bunlar her route'a uygulanmaz, belirli route'lara eklenir.

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

> **Önemli:** Middleware'in önemli özelliklerinden biri request'i durdurabilmesidir. Controller'a ulaşmadan isteği kesebilir.

Örneğin:

```text
Request
   │
   ▼
auth Middleware
   │
   ├── Giriş yapılmış     → Devam et
   │
   └── Giriş yapılmamış   → İsteği durdur / yönlendir
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

Bir route üzerinde birden fazla middleware kullanılabilir.

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

bir route parameter'dır.

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

Yani temel akış:

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

Route **hangi kodun çalışacağını seçer**, Controller ise **request'in nasıl işleneceğini koordine eder**.

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

> **Önemli:** `{{ }}` sadece yazdırmakla kalmaz, varsayılan olarak HTML'i escape eder.

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

Blade bu tarz kontrol yapılarını daha okunabilir şekilde yazmamızı sağlar.

---

## 4.2 Layout Mantığı: `@extends`, `@section`, `@yield`

Blade içerisinde ortak sayfa yapılarını tekrar tekrar yazmak yerine layout kullanabiliriz.

### `@extends`

Hangi layout'u kullanacağını söyler:

```blade
@extends('layouts.app')
```

---

### `@section`

Sayfanın belirli içeriğini tanımlar:

```blade
@section('content')

    <h1>Postlar</h1>

@endsection
```

---

### `@yield`

Layout içerisinde içeriğin geleceği yeri belirtir:

```blade
@yield('content')
```

Kısaca:

```text
@extends → Hangi layout?
@section → Bu sayfanın içeriği ne?
@yield   → O içerik layout'un neresine gelecek?
```

Basit akış:

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

Yani browser:

```text
<script>alert('hack')</script>
```

yazısını görür fakat script çalışmaz.

Bu, **XSS'e karşı önemli bir korumadır.**

---

### `{!! $variable !!}`

Bu kullanım veriyi escape etmeden raw HTML olarak yazdırır.

```blade
{!! $variable !!}
```

Örneğin:

```php
$variable = '<strong>Merhaba</strong>';
```

değeri Blade'e gönderilirse:

```blade
{!! $variable !!}
```

browser bunu HTML olarak yorumlar.

Bu nedenle kullanıcıdan gelen veya güvenmediğimiz verilerde `{!! !!}` kullanmak tehlikeli olabilir.

Kısaca:

| Kullanım            | Davranış                 |
| ------------------- | ------------------------ |
| `{{ $variable }}`   | HTML'i escape eder       |
| `{!! $variable !!}` | Raw HTML olarak yazdırır |

---

# 5. Request

Request, client'ın server'a gönderdiği HTTP isteğini temsil eder.

Örneğin kullanıcı bir form doldurur. Laravel bu isteği bizim rahat kullanabileceğimiz bir `Request` object haline getirir.

Basit akış:

```text
Client
   │
   │ HTTP Request
   ▼
Laravel
   │
   ▼
Request Object
   │
   ▼
Controller
```

Örneğin:

```php
public function store(Request $request)
{
    //
}
```

Buradaki:

```php
$request
```

gelen HTTP request'i temsil eder.

---

## 5.1 `input()` Mantığı

Request içerisindeki belirli bir input değerini almak için kullanılabilir.

```php
$request->input('title');
```

Bu ifade:

> Request içindeki `title` isimli input'un değerini ver.

anlamına gelir.

Örneğin formdan:

```text
title = Laravel
```

geldiyse:

```php
$title = $request->input('title');
```

sonucunda:

```text
$title = "Laravel"
```

olur.

---

## 5.2 `$request->all()`

Request içerisindeki bütün input'ları alabiliriz.

```php
$data = $request->all();
```

Mesela form:

```text
title   = Laravel
content = Request konusu
```

gönderdiyse, `$data` içerisinde bu değerler bulunur.

---

## 5.3 Route Parameter ile Request Input Aynı Şey Değildir

Örneğin route:

```php
Route::get('/posts/{id}', ...);
```

Request:

```text
GET /posts/5
```

Buradaki:

```text
5
```

bir **route parameter**'dır.

Yani:

```text
/posts/{id}
        │
        ▼
        5
```

Ama şu request'te:

```text
/posts?search=laravel
```

içindeki:

```text
search=laravel
```

bir **query parameter**'dır.

Kısaca:

```text
/posts/5
       │
       └── Route Parameter


/posts?search=laravel
       │
       └── Query Parameter
```

---

# 6. Validation

Örnek request:

```php
public function store(Request $request)
{
    $title = $request->input('title');
}
```

Kullanıcı `title` alanını boş gönderebilir, 5000 karakter gönderebilir veya beklemediğimiz türde bir veri gönderebilir.

Validation bunun için vardır.

Validation, gelen verinin belirlediğimiz kurallara uygun olup olmadığını kontrol eder.

Örneğin yeni bir blog postu oluştururken `title` alanının:

```text
Boş olmamasını
String olmasını
255 karakteri geçmemesini
```

isteyebiliriz.

Laravel:

```php
$request->validate([
    'title' => ['required', 'string', 'max:255'],
]);
```

ile bunu kontrol edebilir.

Temel akış:

```text
Request
   │
   ▼
Validation
   │
   ├── Kurallara uygun değil → Hata
   │
   └── Kurallara uygun       → Devam et
```

---

## 6.1 En Temel Validation Kuralları

### `required`

Alan boş olamaz.

```php
'required'
```

---

### `string`

Değerin string olmasını ister.

```php
'string'
```

---

### `max`

Maksimum uzunluğu / değeri belirtir.

```php
'max:255'
```

---

### `min`

Minimum uzunluğu / değeri belirtir.

```php
'min:3'
```

---

### `email`

Değerin geçerli email formatında olmasını kontrol eder.

```php
'email'
```

---

### `integer`

Değerin integer olmasını ister.

```php
'integer'
```

---

### `nullable`

Alan gelmeyebilir veya `null` olabilir.

```php
'nullable'
```

Örneğin:

```php
$request->validate([
    'title' => ['required', 'string', 'max:255'],
    'description' => ['nullable', 'string'],
]);
```

Burada `title` zorunluyken `description` zorunlu değildir.

---

# 7. Model

Laravel'de Model, uygulamadaki bir veritabanı tablosunu PHP tarafında temsil eden class'tır.

Örneğin:

```text
posts
```

tablosu için:

```text
app/Models/Post.php
```

modelini oluşturabiliriz.

Basit ilişki:

```text
Database
   │
   ▼
posts tablosu
   │
   ▼
Post Model
   │
   ▼
PHP Kodumuz
```

---

## 7.1 Hangi Problemi Çözer?

Model olmasaydı veritabanıyla çalışırken doğrudan sürekli SQL yazabilirdik.

Örneğin:

```sql
SELECT * FROM posts;
```

Laravel'de Model sayesinde:

```php
Post::all();
```

yazabiliriz.

Yani database'deki verilerle PHP class'ları üzerinden çalışabiliriz.

---

# 8. Eloquent ORM

ORM, **Object Relational Mapping** demektir.

Temel fikir:

> Database tabloları ve satırlarıyla doğrudan sürekli SQL yazarak değil, PHP object / class yapıları üzerinden çalışmak.

Laravel'in kullandığı ORM sistemi **Eloquent**'tir.

Basit olarak:

```text
PHP
 │
 ▼
Eloquent
 │
 ▼
SQL
 │
 ▼
Database
```

---

## 8.1 Neden Kullanıyoruz?

Eloquent olmasaydı sık sık şu tarz SQL yazabilirdik:

```sql
SELECT *
FROM posts
WHERE id = 5;
```

Laravel'de:

```php
$post = Post::find(5);
```

yazabiliriz.

İkisi de temelde database'den ilgili postu bulmak için kullanılır.

### Avantajları

* Daha okunabilir PHP kodu
* Daha az tekrar
* İlişkilerle daha kolay çalışma
* CRUD işlemlerinin kolaylaşması

> **Önemli:** Eloquent SQL'i ortadan kaldırmaz. Arkada yine SQL sorguları çalışır.

Yani:

```php
Post::find(5);
```

yazdığımızda temel mantık:

```text
Post::find(5)
      │
      ▼
Eloquent
      │
      ▼
SQL sorgusu oluşturulur
      │
      ▼
Database
      │
      ▼
Post verisi
      │
      ▼
Post Object
```

şeklindedir.

---

## Gün 3 İlerleme

* [x] Middleware
* [x] Global Middleware
* [x] Route Middleware
* [x] Birden Fazla Middleware
* [x] Routing
* [x] HTTP Route Metotları
* [x] CRUD ve HTTP Metotları
* [x] Controller
* [x] Blade
* [x] Blade Layout Yapısı
* [x] Blade Escape ve XSS
* [x] Request
* [x] Request Input
* [x] Route ve Query Parameter
* [x] Validation
* [x] Temel Validation Kuralları
* [x] Model
* [x] Eloquent ORM
