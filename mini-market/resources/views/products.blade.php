<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Market Ürünleri</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #1f2937;
        }

        main {
            width: min(960px, calc(100% - 32px));
            margin: 32px auto;
        }

        h1 {
            margin-bottom: 8px;
            font-size: 28px;
        }

        .subtitle {
            margin-top: 0;
            color: #6b7280;
        }

        .panel {
            margin-top: 24px;
            padding: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        label {
            display: grid;
            gap: 6px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font: inherit;
        }

        .actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        button {
            padding: 10px 14px;
            border: 0;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font: inherit;
            font-weight: 700;
        }

        button.secondary {
            background: #64748b;
        }

        button.edit {
            background: #059669;
        }

        table {
            width: 100%;
            margin-top: 16px;
            border-collapse: collapse;
            background: #ffffff;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .message {
            min-height: 24px;
            margin-top: 12px;
            font-weight: 700;
        }

        button.delete {
            background: #dc2626;
        }

        @media (max-width: 720px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>Mini Market Ürünleri</h1>
        <p class="subtitle">Bu basit ekran Laravel API endpoint'lerini kullanarak ürün ekler, listeler ve günceller.</p>

        <section class="panel">
            <h2 id="form-title">Yeni Ürün Ekle</h2>

            <form id="product-form">
                <div class="form-grid">
                    <label>
                        Ürün Adı
                        <input id="name" type="text" required>
                    </label>

                    <label>
                        Fiyat
                        <input id="price" type="number" min="0" step="0.01" required>
                    </label>

                    <label>
                        Stok
                        <input id="stock" type="number" min="0" step="1" required>
                    </label>
                </div>

                <div class="actions">
                    <button id="submit-button" type="submit">Kaydet</button>
                    <button class="secondary" id="cancel-button" type="button" hidden>Vazgeç</button>
                </div>

                <p class="message" id="message"></p>
            </form>
        </section>

        <section class="panel">
            <h2>Ürün Listesi</h2>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ürün</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody id="products-table"></tbody>
            </table>
        </section>
    </main>

    <script>
        const form = document.querySelector('#product-form');
        const formTitle = document.querySelector('#form-title');
        const nameInput = document.querySelector('#name');
        const priceInput = document.querySelector('#price');
        const stockInput = document.querySelector('#stock');
        const submitButton = document.querySelector('#submit-button');
        const cancelButton = document.querySelector('#cancel-button');
        const message = document.querySelector('#message');
        const productsTable = document.querySelector('#products-table');

        let editingProductId = null;

        async function loadProducts() {
            const response = await fetch('/api/products');
            const products = await response.json();

            productsTable.innerHTML = '';

            products.forEach((product) => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.price}</td>
                    <td>${product.stock}</td>
                    <td>
                        <button class="edit" type="button">Düzenle</button>
                        <button class="delete" type="button">Sil</button>
                    </td>
                `;

                row.querySelector('.edit').addEventListener('click', () => {
                    startEdit(product);
                });

                row.querySelector('.delete').addEventListener('click', () => {
                    deleteProduct(product.id);
                });

                productsTable.appendChild(row);
            });
        }

        function startEdit(product) {
            editingProductId = product.id;
            formTitle.textContent = 'Ürün Güncelle';
            submitButton.textContent = 'Güncelle';
            cancelButton.hidden = false;

            nameInput.value = product.name;
            priceInput.value = product.price;
            stockInput.value = product.stock;
            message.textContent = '';
        }

        async function deleteProduct(productId) {
            const shouldDelete = confirm('Bu ürünü silmek istiyor musun?');

            if (!shouldDelete) {
                return;
            }

            const response = await fetch(`/api/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                message.textContent = 'Ürün silinemedi.';
                return;
            }

            message.textContent = 'Ürün silindi.';
            resetForm();
            await loadProducts();
        }

        function resetForm() {
            editingProductId = null;
            form.reset();
            formTitle.textContent = 'Yeni Ürün Ekle';
            submitButton.textContent = 'Kaydet';
            cancelButton.hidden = true;
            message.textContent = '';
        }

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const productData = {
                name: nameInput.value,
                price: Number(priceInput.value),
                stock: Number(stockInput.value),
            };

            const url = editingProductId
                ? `/api/products/${editingProductId}`
                : '/api/products';

            const method = editingProductId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(productData),
            });

            if (!response.ok) {
                message.textContent = 'İşlem başarısız oldu. Alanları kontrol et.';
                return;
            }

            message.textContent = editingProductId
                ? 'Ürün güncellendi.'
                : 'Ürün eklendi.';

            resetForm();
            await loadProducts();
        });

        cancelButton.addEventListener('click', resetForm);

        loadProducts();
    </script>
</body>
</html>
