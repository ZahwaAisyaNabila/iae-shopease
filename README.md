# ShopEase — Microservices Integration

Backend demonstrasi marketplace untuk tugas besar Integrasi Aplikasi Enterprise. Stack ini memenuhi REST, GraphQL, Docker, database terpisah, dan Redis message broker.

## Jalankan

```bash
docker compose up --build
```

Service tersedia di `localhost:8001` (user), `8002` (order), `8003` (product/GraphQL), dan `8004` (notification).

## Arsitektur

```text
Client -> User Service (REST) -> user-db
Client -> Product Service (GraphQL + REST) -> product SQLite
Client -> Order Service (REST) -> order-db
                          | publish OrderCreated
                          v
                       Redis Pub/Sub
                          |
                          v
                Notification Service (Redis consumer + REST/queue) -> notification-db
```

Setiap service memiliki ownership database sendiri; ID user dan produk hanya direferensikan sebagai nilai, tanpa foreign key lintas database.

## API demo

```bash
# Ganti setiap placeholder "..." sesuai data uji Anda.
# Buat user
curl -X POST http://localhost:8001/api/v1/users -H "Content-Type: application/json" -d '{"name":"...","email":"...","password":"..."}'

# Buat produk melalui GraphQL
curl http://localhost:8003/graphql -H "Content-Type: application/json" -d '{"query":"mutation { createProduct(name: \"...\", price: 0, stock: 0) { id name price stock } }"}'

# Pesan produk. Request ini menyimpan order dan menerbitkan event OrderCreated ke Redis.
curl -X POST http://localhost:8002/api/orders -H "Content-Type: application/json" -d '{"user_id":0,"product_id":0,"quantity":0,"total_price":0}'

# Consumer `notification-event-consumer` otomatis membuat notifikasi ketika menerima OrderCreated.
# Endpoint manual untuk membuat notifikasi juga tersedia:
curl -X POST http://localhost:8004/api/notifications/send -H "Content-Type: application/json" -d '{"user_id":0,"type":"...","recipient_channel":"...","message":"..."}'
```

GraphQL playground tersedia bila paket Lighthouse mengaktifkannya; endpoint standar adalah `POST /graphql`. REST produk ada di `/api/products`.

## Postman

Import [koleksi Postman](postman/ShopEase.postman_collection.json) lalu jalankan request dengan urutan: **Create user 1 sampai 4**, **Create product (GraphQL)**, **Create order**, dan **List notifications**. Collection secara otomatis menyimpan `user_id`, `product_id`, dan `order_id` dari respons create untuk dipakai request berikutnya. Request bertanda `FAIL` mendemonstrasikan respons validasi, data duplikat, atau data tidak ditemukan.
