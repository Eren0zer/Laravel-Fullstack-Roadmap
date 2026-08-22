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

Ama sorguyu yanlış şekilde, kullanıcı input'unu direkt string içine ekleyerek oluşturursak:

```php
$email = $request->input('email');

$sql = "SELECT * FROM users WHERE email = '$email'";
```

burada kullanıcı gönderdiği input ile SQL sorgusunun kendisini etkileyebilir.

Basit olarak:

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

### Olası Sonuçları

SQL Injection'ın sonuçları ciddi olabilir:

* Yetkisiz veri okuma
* Yetkilendirme kontrollerini aşma
* Veri değiştirme
* Veri silme

Örneğin bazı SQL Injection payload'ları:

```text
' or 1=1--
' or 1=1/*
') or '1'='1--
') or ('1'='1--
```

Buradaki temel problem, kullanıcı verisinin artık sadece **veri** olarak değil, SQL sorgusunun bir parçası olarak yorumlanabilmesidir.

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

Basit mantık:

```text
SQL
 │
 │ SELECT * FROM users WHERE email = ?
 │
 ▼
Database
 ▲
 │
 │ ali@example.com
 │
DATA
```

Bu sayede kullanıcı, girdiği değer içinde ayrı bir SQL komutu göndermeye çalışsa bile bunun SQL syntax'ı olarak çalışması engellenmeye yardımcı olunur.

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

Burada `$email` değeri binding olarak gönderilir.

---

### Validation SQL Injection'ı Tamamen Engellemez

Örneğin:

```php
$request->validate([
    'email' => ['required', 'email'],
]);
```

koduyla email alındığını düşünelim.

Burada yapılan şey öncelikle format kontrolüdür.

Validation önemlidir fakat SQL Injection'a karşı asıl koruma olarak düşünülmemelidir.

> **Önemli:** Parameter Binding, kullanıcıdan gelen veriyi SQL sorgusunun kod kısmından ayrı olarak database'e göndermemizi sağlar. Böylece kullanıcı verisinin SQL syntax'ı olarak yorumlanmasını engellemeye yardımcı olur ve SQL Injection riskini azaltır.

Kısaca:

```text
Validation
    │
    └── Veri kurallara uygun mu?


Parameter Binding
    │
    └── Kullanıcı verisi SQL kodundan ayrı mı?
```

---

## 3. Raw Query Riskleri

Raw query, Laravel'in sunduğu güvenli abstraction'ı kısmen bırakıp SQL'i daha doğrudan yazmandır.

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

Bu da raw SQL'dir fakat binding kullandığı için güvenli şekilde kullanılabilir.

Yani önemli nokta:

```text
Raw Query
   │
   ├── Binding kullanıyor      → Daha güvenli kullanım
   │
   └── Input string'e gömülüyor → Riskli
```

> **Önemli:** Raw query kullanmak otomatik olarak güvensiz değildir. Kullanıcı verisini raw SQL string'inin içine direkt gömmek risklidir.

Riskli mantık:

```php
$sql = "SELECT * FROM users WHERE email = '$email'";
```

Daha güvenli mantık:

```php
DB::select(
    'SELECT * FROM users WHERE email = ?',
    [$email]
);
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

Bu normaldir.

Ama saldırgan şuna benzer bir HTML göndermeye çalışabilir:

```html
<script>alert('XSS')</script>
```

Eğer uygulama bunu güvenli şekilde escape etmeden HTML olarak browser'a gönderirse, browser script'i çalıştırabilir.

Basit akış:

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

Kısaca:

```text
SQL Injection
      │
      └── Database / SQL sorgusu


XSS
 │
 └── Browser / HTML / JavaScript
```

---
