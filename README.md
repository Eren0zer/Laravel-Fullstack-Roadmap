# Laravel Fullstack Roadmap

PHP, Laravel, SQL, REST API, güvenlik, clean code ve Next.js entegrasyonunu uygulamalı projeler üzerinden öğrenmek için hazırlanmış yapılandırılmış bir yol haritası.

> Önemli not: Notlarımı aldıktan sonra GitHub'a yüklemeden önce okunabilirliği ve görselliği artırmak için AI ile düzenleme yapıyorum.

## Proje Amacı

Bu repoda Laravel + Next.js kullanarak sıfırdan bir mini market sistemi geliştiriyorum.

Amaç sadece çalışan bir proje yapmak değil; aynı zamanda her adımda kullanılan yapıları öğrenmek:

- Laravel backend mantığı
- REST API oluşturma
- Controller, model, migration ve seeder kullanımı
- Veritabanından veri okuma
- İlerleyen aşamalarda Next.js frontend ile API tüketimi

## Proje Yapısı

```text
Proje/
  mini-market/   Laravel backend projesi
```

İlerleyen adımlarda Next.js frontend uygulaması da aynı repoya eklenecek.

## Şu Ana Kadar Yapılanlar

### 1. Laravel Projesi Oluşturuldu

Laravel projesi şu komut ile oluşturuldu:

```bash
composer create-project laravel/laravel mini-market
```

Bu komut `mini-market` adında yeni bir Laravel projesi oluşturdu.

### 2. Laravel Geliştirme Sunucusu Çalıştırıldı

Proje klasörüne girildi:

```bash
cd mini-market
```

Laravel geliştirme sunucusu başlatıldı:

```bash
php artisan serve
```

Tarayıcıdan şu adrese girilerek Laravel'in çalıştığı kontrol edildi:

```text
http://127.0.0.1:8000
```

### 3. API Route Dosyası Oluşturuldu

Laravel 12 ile gelen kurulumda başlangıçta sadece şu route dosyaları vardı:

```text
routes/web.php
routes/console.php
```

API route dosyasını oluşturmak için şu komut çalıştırıldı:

```bash
php artisan install:api
```

Bu işlemden sonra `routes/api.php` dosyası oluşturuldu.

### 4. İlk Test API Endpoint'i Yazıldı

`routes/api.php` içine test amaçlı bir endpoint eklendi:

```php
Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API çalışıyor'
    ]);
});
```

Kontrol adresi:

```text
http://127.0.0.1:8000/api/test
```

Bu endpoint JSON cevap döndürdü.

### 5. Ürün Listeleme Endpoint'i Oluşturuldu

İlk başta ürünler doğrudan route içinde sahte veri olarak döndürüldü.

Sonrasında daha doğru bir yapı için controller oluşturuldu:

```bash
php artisan make:controller Api/ProductController
```

Oluşan dosya:

```text
app/Http/Controllers/Api/ProductController.php
```

`routes/api.php` içinde ürün listeleme route'u controller'a bağlandı:

```php
Route::get('/products', [ProductController::class, 'index']);
```

### 6. Product Modeli ve Migration Oluşturuldu

Ürünleri veritabanında tutmak için model ve migration oluşturuldu:

```bash
php artisan make:model Product -m
```

Bu komut iki temel dosya oluşturdu:

```text
app/Models/Product.php
database/migrations/..._create_products_table.php
```

Migration içinde `products` tablosu için temel alanlar tanımlandı:

```php
$table->id();
$table->string('name');
$table->decimal('price', 10, 2);
$table->integer('stock')->default(0);
$table->timestamps();
```

Migration çalıştırıldı:

```bash
php artisan migrate
```

### 7. Product Modeli Düzenlendi

`Product` modelinde toplu veri eklenebilmesi için `$fillable` alanı tanımlandı:

```php
protected $fillable = [
    'name',
    'price',
    'stock',
];
```

Bu sayede `Product::create()` ile ürün oluştururken bu alanlara veri yazılmasına izin verildi.

### 8. Ürünler Veritabanından Okunmaya Başlandı

`ProductController` içinde ürünler artık sabit diziden değil, veritabanından okunuyor:

```php
public function index(): JsonResponse
{
    $products = Product::all();

    return response()->json($products);
}
```

Kontrol adresi:

```text
http://127.0.0.1:8000/api/products
```

### 9. Seeder ile Örnek Ürünler Eklendi

Örnek ürünleri veritabanına eklemek için seeder oluşturuldu:

```bash
php artisan make:seeder ProductSeeder
```

Oluşan dosya:

```text
database/seeders/ProductSeeder.php
```

Seeder içinde örnek ürünler eklendi:

```php
Product::create([
    'name' => 'Ekmek',
    'price' => 15,
    'stock' => 50,
]);
```

Seeder çalıştırıldı:

```bash
php artisan db:seed --class=ProductSeeder
```

Sonrasında `/api/products` endpoint'i veritabanındaki ürünleri JSON olarak döndürmeye başladı.

### 10. Tek Ürün Getirme Endpoint'i Eklendi

Tek bir ürünü görüntülemek için `show` metodu yazıldı:

```php
public function show(Product $product): JsonResponse
{
    return response()->json($product);
}
```

Route tanımı:

```php
Route::get('/products/{product}', [ProductController::class, 'show']);
```

Kontrol adresi:

```text
http://127.0.0.1:8000/api/products/1
```

Bu adımda Laravel'in Route Model Binding özelliği kullanıldı.

## Öğrenilen Kavramlar

- Laravel projesi oluşturma
- `artisan serve` ile geliştirme sunucusu çalıştırma
- API route mantığı
- JSON response döndürme
- Controller kullanımı
- Model kullanımı
- Migration ile tablo oluşturma
- Seeder ile örnek veri ekleme
- Eloquent ORM ile veritabanından veri okuma
- Route Model Binding

## Sıradaki Adım

Bir sonraki aşamada ürün ekleme işlemi yapılacak:

```text
POST /api/products
```

Bu adımda request verisi alma, validation ve veritabanına yeni kayıt ekleme konuları öğrenilecek.
