# Gün 1 — Web / PHP / SQL Hızlı Tekrar

> Web, PHP ve SQL konularında hızlı tekrar notları.

---

## 1. WEB

### Client Nedir?

Bir servisten istekte bulunan taraftır. Tarayıcı, mobil uygulama gibi örnekleri vardır.

### Server Nedir?

Clientten gelen istekleri karşılayan ve cevap dönen taraftır. Örneğin veri getirir.

### Backend Ne Yapar?

Uygulamanın kullanıcı tarafından doğrudan görülmeyen kısmıdır genellikle: Database ile etkileşime girer, API sağlar, veriyi işler ve frontende gönderir.

**Örneğin:**

```text
Frontend
   │
   │ GET /users/15
   ▼
Backend
   │
   ▼
Database
   │
   ▼
Backend
   │
   │ JSON response
   ▼
Frontend
```

### Frontend Ne Yapar?

Kullanıcının gördüğü ve etkileşim kurduğu kısımdır. Butonlar, sayfalar, tablolar gibi arayüzleri oluşturur. API'lere istek gönderir. Gelen veriyi de kullanıcıya gösterir.

### Full Stack Nedir?

Frontendi de Backendi de yazabilen kişilere denir.

### Monolith Nedir?

Uygulamanın ana parçalarının tek bir uygulama veya proje içerisinde birlikte çalıştığı mimaridir. Backend ve Frontend görevlerini çoğunlukla aynı uygulamanın içinde bulundurur.

```text
Browser
   │
   ▼
Laravel
   │
   ├── Controller  → isteği karşılar
   ├── Model       → veritabanıyla çalışır
   ├── Blade       → HTML üretir
   └── Database
```

---

## 2. HTTP

### HTTP Metotları

|  Method  | Görevi                         |
| :------: | ------------------------------ |
|   `GET`  | veri getir                     |
|  `POST`  | veri oluştur                   |
|   `PUT`  | kaynağı tamamen güncelle       |
|  `PATCH` | kaynağın bir bölümünü güncelle |
| `DELETE` | sil                            |

### Status Kodları

|  Kod  | Tür           | Açıklama                                                |
| :---: | ------------- | ------------------------------------------------------- |
| `1xx` | Informational | İstek alındı, işlem devam ediyor.                       |
| `2xx` | Success       | İstek başarılı.                                         |
| `3xx` | Redirection   | Başka bir yere yönlendirme var.                         |
| `4xx` | Client Error  | Hata büyük ölçüde client tarafındaki istekten kaynaklı. |
| `5xx` | Server Error  | Hata server tarafında.                                  |

#### 2xx — Success

```text
200 OK
201 Created
204 No Content
```

#### 3xx — Redirection

```text
301 Moved Permanently
302 Found
```

#### 4xx — Client Error

```text
400 Bad Request

401 Unauthenticated
└── kullanıcı bilinmiyor

403 Forbidden
└── Kullanıcı biliniyor ancak yetki yok

404 Not Found
422 Validation Error
```

#### 5xx — Server Error

```text
500 Internal Server Error
502 Bad Gateway
503 Service Unavailable
```

---

## 3. Cookie / Session / Token

### Cookie

Browser tarafında saklanan küçük veri.

### Session

Server tarafından kullanıcıya ait durum tutulur. Browser genellikle session ID'yi cookie ile taşır.

### Token

Client her istekte kimliğini kanıtlayan bir token gönderir.

---

## 4. PHP Tekrar

PHP OOP kavramları için kullanılan örnek class:

```php
class User
{
    public function __construct(
        public string $name,
        private string $password
    ) {}

    public function getName(): string
    {
        return $this->name;
    }
}
```

### 4.1 Class

Nesnenin şablonudur.

```php
class User
{
}
```

---

### 4.2 Object

Classtan oluşturulan gerçek örnektir.

```php
$user = new User("Ahmet", "1234");
```

Burada `$user` bir objecttir.

---

### 4.3 Property

Objectin tuttuğu verilerdir.

Yukarıdaki örnek için `name` ve `password` bir propertydir.

```php
public string $name;
private string $password;
```

---

### 4.4 Method

Class içerisinde bulunan fonksiyondur.

`getName()` bir methodtur.

```php
public function getName(): string
{
    return $this->name;
}
```

---

### 4.5 Constructor

Object oluşturulduğu anda otomatik çalışan özel methoddur.

```php
public function __construct(
    public string $name,
    private string $password
) {}
```

`public function __construct` buna örnektir.

---

### 4.6 `$this`

Şu an üzerinde işlem yaptığımız objecti ifade eder.

```php
public function getName(): string
{
    return $this->name;
}
```

Buradaki `$this`, şu an üzerinde işlem yapılan objecti ifade eder.

---

### 4.7 Public

Her yerden erişilebilir.

```php
public string $name;
```

Örneğin:

```php
echo $user->name; // çalışır
```

---

### 4.8 Private

Sadece o classın içerisinden erişilebilir.

```php
private string $password;
```

Class dışından erişilmeye çalışılırsa:

```php
echo $user->password; // çalışmaz
```

---

### 4.9 Protected

Private gibi dışarıdan erişilemez fakat child classlar erişebilir.

```php
protected string $example;
```

---

### 4.10 Inheritance

Bir class başka bir classın özelliklerini ve methodlarını miras alabilir.

```php
class Admin extends User
{
}
```

---

### 4.11 Type Hint

Fonksiyona veya propertyye hangi tipte veri gelmesi gerektiğini belirtirsin.

```php
function findUser(int $id)
{
}
```

Burada `$id` değerinin `int` olması gerektiği belirtilmiştir.

---

### 4.12 Return Type

Fonksiyonun ne döndüreceğini belirtir.

```php
function getName(): string
{
    return "Ahmet";
}
```

Fonksiyonun `string` döndürmesi gerekir.

---

## İlerleme

* [x] Web
* [x] HTTP
* [x] Cookie / Session / Token
* [x] PHP OOP Temelleri
* [ ] SQL

---

> Gün 1 tamamlandıkça ve yeni konular eklendikçe notlar güncellenecektir.
