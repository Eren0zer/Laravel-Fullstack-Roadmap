# Gün 4 — Security

## 1. SQL Injection

SQL Injection, kullanıcıdan gelen verinin SQL sorgusunun yapısını değiştirebilmesi durumudur.

Mesela kullanıcı:

```text
email = ali@example.com
```

gönderir.

Biz database'e:

```sql
SELECT *
FROM users
WHERE email = 'ali@example.com';
```

sorgusunu göndeririz.

Buraya kadar sorun yok.

Ama sorguyu yanlış şekilde kullanıcı input'unu string içine ekleyerek oluşturursak:

```php
$email = $request->input('email');

$sql = "SELECT * FROM users WHERE email = '$email'";
```

burada kullanıcı gönderdiği input ile SQL sorgusunun kendisini etkileyebilir.

SQL Injection'ın sonuçları ciddi olabilir:

* Yetkisiz veri okuma
* Yetkilendirme kontrollerini aşma
* Veri değiştirme
* Veri silme

Örneğin birkaç SQL Injection payload'ı:

```text
' or 1=1--
' or 1=1/*
') or '1'='1--
') or ('1'='1--
```

Temel problem:

```text
User Input
    │
    ▼
SQL String İçine Direkt Eklenir
    │
    ▼
SQL Sorgusunun Yapısı Değişebilir
    │
    ▼
SQL Injection
```

---

## 2. Parameter Binding

Elimizdeki sorgunun şu şekilde olduğunu düşünelim:

```sql
SELECT *
FROM users
WHERE email = ?
```

Buradaki:

```text
?
```

bir **placeholder**'dır.

Sonra değeri ayrıca göndeririz:

```text
SQL:
SELECT * FROM users WHERE email = ?

DATA:
ali@example.com
```

Yani:

```text
SQL kodu ayrı
Kullanıcı verisi ayrı
```

Database kullanıcı verisini SQL komutu olarak değil, değer olarak işler.

Bu sayede kullanıcı girdiği değer içinde ayrı bir SQL komutu gönderse bile bunun çalışmasını engellemeye yardımcı olur.

Laravel'de Query Builder kullanıldığında çoğu zaman framework tarafında binding otomatik yapılır.

Örneğin:

```php
$user = DB::table('users')
    ->where('email', $email)
    ->first();
```

Eloquent'te de aynı mantık kullanılır:

```php
$user = User::where('email', $email)->first();
```

Burada `$email` binding olarak gönderilir.

---

### Validation SQL Injection'ı Tamamen Engellemez

Örneğin:

```php
$request->validate([
    'email' => ['required', 'email'],
]);
```

koduyla email alındığını düşünelim.

Burada yapılan şey format kontrolüdür. Validation yapmak SQL Injection'ı tamamen engellemez.

> **Önemli:** Parameter Binding, kullanıcıdan gelen veriyi SQL sorgusunun kod kısmından ayrı olarak database'e göndermemizi sağlar. Böylece kullanıcı verisinin SQL syntax'ı olarak yorumlanmasını engellemeye yardımcı olur ve SQL Injection riskini azaltır.

Kısaca:

```text
Validation
    │
    └── Veri belirlenen kurallara uygun mu?


Parameter Binding
    │
    └── Kullanıcı verisi SQL kodundan ayrı mı?
```

---

## 3. Raw Query Riskleri

Raw query, Laravel'in sana sunduğu güvenli abstraction'ı kısmen bırakıp SQL'i daha doğrudan yazmandır.

Normal Eloquent:

```php
User::where('email', $email)->first();
```

Query Builder:

```php
DB::table('users')
    ->where('email', $email)
    ->first();
```

Bunlarda Laravel çoğu değeri senin adına bind eder.

Ama bazen doğrudan SQL yazabilirsin:

```php
DB::select(
    'SELECT * FROM users WHERE email = ?',
    [$email]
);
```

Bu da raw SQL'dir ama binding kullandığı için güvenli şekilde kullanılabilir.

Yani önemli nokta:

> **Raw query kullanmak otomatik olarak güvensiz değildir. Kullanıcı verisini raw SQL string'ine gömmek risklidir.**

Kısaca:

```text
Raw Query
   │
   ├── Binding kullanılıyor       → Daha güvenli
   │
   └── Input SQL string'e gömülü  → Riskli
```

---

# 4. XSS

XSS, **Cross-Site Scripting** demektir.

Temel fikir:

> Kullanıcının gönderdiği zararlı HTML / JavaScript'in başka bir kullanıcının browser'ında çalışması.

Örneğin bir yorum alanı düşünelim.

Normal yorum:

```text
Merhaba
```

Ama saldırgan şuna benzer HTML göndermeye çalışabilir:

```html
<script>alert('XSS')</script>
```

Eğer uygulama bunu güvenli şekilde escape etmeden HTML olarak browser'a gönderirse browser script'i çalıştırabilir.

Akış:

```text
Saldırgan
   │
   │ Zararlı HTML / JavaScript
   ▼
Uygulama
   │
   │ Escape edilmeden gönderilir
   ▼
Browser
   │
   ▼
Script çalışır
```

---

## 4.1 Neden Tehlikeli?

XSS sadece ekranda:

```javascript
alert('XSS');
```

göstermek değildir.

Gerçek saldırıda JavaScript browser tarafında çalıştığı için örneğin:

* Kullanıcı adına işlemler yaptırma
* Sayfa içeriğini değiştirme
* Sahte form gösterme
* Erişebildiği verileri dışarı gönderme

gibi etkileri olabilir.

Burada önemli nokta:

> **XSS'in hedefi esas olarak database değil, browser'dır.**

```text
SQL Injection
      │
      └── SQL / Database


XSS
 │
 └── Browser / HTML / JavaScript
```

---

## 4.2 Stored XSS

Stored XSS, zararlı içeriğin uygulamada kalıcı olarak saklanması ve daha sonra başka kullanıcılar sayfayı açtığında çalışmasıdır.

Örneğin blogundaki yorum sistemi:

```text
Saldırgan yorum gönderir
        │
        ▼
Yorum database'e kaydedilir
        │
        ▼
Başka kullanıcı post sayfasını açar
        │
        ▼
Yorum HTML'e raw basılır
        │
        ▼
Zararlı JavaScript çalışır
```

Mesela saldırganın yorumu:

```html
<script>alert('XSS')</script>
```

Database'e normal bir yorum gibi kaydedilmiş olsun.

Blade'de:

```blade
{!! $comment->content !!}
```

şeklinde gösterirsen risk oluşur.

Ama:

```blade
{{ $comment->content }}
```

kullanırsan Blade içeriği escape eder.

Stored XSS'in önemli özelliği şudur:

> Payload bir kere sisteme girer ve kalıcı olarak saklandığı için aynı zararlı içerik birçok kullanıcıya gösterilebilir.

---

## 4.3 Reflected XSS

Reflected XSS'te zararlı veri genellikle database'e kaydedilmez.

Request ile gelir ve aynı response içerisinde kullanıcıya geri yansıtılır.

Örneğin arama sayfan:

```text
/search?q=laravel
```

Blade:

```blade
<p>Arama sonucu: {{ $search }}</p>
```

Bu güvenli taraftadır.

Ama uygulama kullanıcıdan gelen değeri raw HTML olarak response'a basarsa risk oluşabilir.

Akış:

```text
Request'te zararlı input
        │
        ▼
Server
        │
        ▼
Database'e kaydedilmez
        │
        ▼
Aynı response'a eklenir
        │
        ▼
Browser yorumlar
```

Bu yüzden adına **reflected**, yani "yansıtılmış" XSS denir.

### Stored ve Reflected XSS Farkı

```text
Stored XSS
    │
    └── Zararlı veri kalıcı olarak saklanır


Reflected XSS
    │
    └── Zararlı veri request'ten gelir ve response'a yansıtılır
```

---

# 5. CSRF

CSRF, **Cross-Site Request Forgery** demektir.

Temel fikir:

> Kullanıcının giriş yaptığı bir uygulamaya, kullanıcının haberi olmadan başka bir site üzerinden istek göndertmek.

Basit akış:

```text
Kullanıcı uygulamada giriş yapmış
        │
        ▼
Session aktif
        │
        ▼
Kullanıcı başka bir siteyi açar
        │
        ▼
Bu site hedef uygulamaya request göndertmeye çalışır
        │
        ▼
Browser session bilgisini de gönderebilir
```

---

## 5.1 Laravel Bunu Nasıl Çözüyor?

Laravel web formlarında bir CSRF token kullanır.

Blade:

```blade
<form method="POST" action="/posts">
    @csrf

    <input type="text" name="title">
</form>
```

`@csrf` yaklaşık olarak gizli bir input üretir:

```html
<input type="hidden" name="_token" value="random-token">
```

Yani request sadece form verisini değil, CSRF token'ı da gönderir.

```text
Form
 │
 ├── title
 │
 └── _token
        │
        ▼
Laravel
        │
        ▼
Token geçerli mi?
```

---

## 5.2 Neden Cookie Tek Başına Yetmiyor?

Çünkü browser cookie'yi çoğu durumda otomatik gönderir.

Yani saldırgan kullanıcının session cookie değerini bilmek zorunda olmayabilir.

Kullanıcının browser'ını request göndermeye ikna etmek yeterli olabilir.

CSRF token ise saldırganın başka domain'den kolayca bilemeyeceği ek bir değer sağlar.

Kısaca:

```text
Session Cookie
     │
     └── Browser tarafından otomatik gönderilebilir


CSRF Token
     │
     └── Request'in uygulamanın kendi formundan geldiğini
         doğrulamaya yardımcı olur
```

---

# 6. Authentication Security

**Authentication** ve **Authorization** aynı şey değildir.

```text
Authentication → "Sen kimsin?"

Authorization  → "Bunu yapmaya yetkin var mı?"
```

Middleware notunda gördüğümüz `auth` middleware'i, giriş yapmamış kullanıcının korunan route'a ulaşmasını engelleyebiliyordu.

Uygulama kabaca:

```text
POST /login
     │
     ▼
Kullanıcıyı email ile bul
     │
     ▼
Gönderilen password doğru mu?
     │
     ▼
    Evet
     │
     ▼
Authenticated session oluştur
```

---

## 6.1 Database'de Password Nasıl Tutulmalı?

Password kesinlikle düz metin olarak tutulmamalıdır.

Örneğin şu şekilde saklamak:

```text
email:    ali@example.com
password: 123456
```

güvenli değildir.

Database sızarsa bütün kullanıcıların gerçek şifreleri doğrudan ortaya çıkar.

Bu yüzden password **hash** olarak saklanır.

Laravel'de bunun için genellikle `Hash` sistemi kullanılır:

```php
use Illuminate\Support\Facades\Hash;

$hashedPassword = Hash::make($request->password);
```

Database'de gerçek password yerine hash değeri saklanır.

Basit olarak:

```text
Password
   │
   │ "123456"
   ▼
Hash::make()
   │
   ▼
Hash Değeri
   │
   ▼
Database
```

> **Önemli:** Hash ile encryption aynı şey değildir. Password için encryption değil, **password hashing** kullanılır.

Kısaca:

```text
Password
   │
   ▼
Hash
   │
   ▼
Database
```

Gerçek password doğrudan database'e yazılmaz.

---

## Gün 4 İlerleme

* [x] SQL Injection
* [x] Parameter Binding
* [x] Validation ve SQL Injection Farkı
* [x] Raw Query Riskleri
* [x] XSS
* [x] Stored XSS
* [x] Reflected XSS
* [x] Blade Escape Mantığı
* [x] CSRF
* [x] Laravel `@csrf`
* [x] Authentication
* [x] Authorization
* [x] Password Hashing
