# ShopEase Microservices - Briefing Presentasi

## 1. Identitas Kelompok

| Anggota | NIM | Service |
|---|---:|---|
| Alya Fazaristi | 102062400049 | Notification Service |
| Karin Zalfa Santoso | 102062400118 | Order Service |
| Rr Khaila Widyaloka | 102062400066 | User Service |
| Zahwa Aisya Nabila | 102062400046 | Product Service |

## 2. Deskripsi Project

ShopEase adalah backend marketplace sederhana berbasis microservices. Sistem memisahkan pengelolaan user, produk, order, dan notifikasi menjadi empat service mandiri. Setiap service memiliki tanggung jawab dan penyimpanan data sendiri.

Tujuan implementasi ini adalah menunjukkan integrasi aplikasi enterprise melalui REST API, GraphQL manual, Hasura GraphQL, Docker, database terpisah, serta Redis sebagai message broker.

## 3. Teknologi

| Kebutuhan | Implementasi ShopEase |
|---|---|
| Containerization | Docker Compose |
| Framework | Laravel/PHP |
| REST API | User, Product, Order, Notification Service |
| GraphQL backend framework | Lighthouse pada Product Service |
| GraphQL Hasura | Hasura pada User Service |
| Message broker | Redis Pub/Sub |
| Database User | PostgreSQL |
| Database Product | SQLite |
| Database Order | MySQL |
| Database Notification | MySQL |

## 4. Arsitektur Sistem

```text
                         +---------------------+
                         | Client / Postman    |
                         +----------+----------+
                                    |
             +----------------------+----------------------+
             |                      |                      |
             v                      v                      v
  +-------------------+  +-------------------+  +-------------------+
  | User Service      |  | Product Service   |  | Order Service     |
  | REST :8001        |  | REST/GraphQL :8003|  | REST :8002        |
  +---------+---------+  +---------+---------+  +---------+---------+
            |                      |                      |
            v                      v                      | publish OrderCreated
      PostgreSQL                 SQLite                   v
            |                                      +---------------+
            v                                      | Redis Pub/Sub |
  +-------------------+                            +-------+-------+
  | Hasura :8080      |                                    |
  | GraphQL User DB   |                                    | subscribe
  +-------------------+                                    v
                                                  +---------------------+
                                                  | Notification Service|
                                                  | REST :8004          |
                                                  +----------+----------+
                                                             |
                                                             v
                                                           MySQL
```

Prinsip microservices yang digunakan:

- Setiap service memiliki database sendiri.
- Order hanya menyimpan `user_id` dan `product_id` sebagai referensi nilai; tidak ada foreign key lintas database.
- Komunikasi sinkron dilakukan dengan REST.
- Komunikasi asinkron Order ke Notification dilakukan dengan event Redis `OrderCreated`.

## 5. Jalankan Sistem

```powershell
cd C:\laragon\www\iae-shopease
docker compose up --build
```

Verifikasi container:

```powershell
docker compose ps
```

| Komponen | URL/Port |
|---|---|
| User Service | http://localhost:8001 |
| Product Service | http://localhost:8003 |
| Order Service | http://localhost:8002 |
| Notification Service | http://localhost:8004 |
| Hasura Console | http://localhost:8080 |
| Redis | localhost:6379 |

## 6. Naskah Presentasi per Anggota

### Rr Khaila Widyaloka - User Service dan Hasura

"Saya menangani User Service. Service ini menyediakan REST API untuk membuat, menampilkan daftar, dan melihat detail user. Data user disimpan pada PostgreSQL, sehingga database user terpisah dari database service lain. Selain REST, tabel users diekspos menggunakan Hasura GraphQL pada port 8080. Hasura terhubung langsung ke PostgreSQL dan tabel users telah di-track."

REST endpoint:

```text
POST http://localhost:8001/api/v1/users
GET  http://localhost:8001/api/v1/users
GET  http://localhost:8001/api/v1/users/{id}
```

Demo REST: jalankan empat request create user pada folder `1. User Service` di Postman, lalu jalankan `List users`.

Demo Hasura:

1. Buka `http://localhost:8080` dan masukkan admin secret `secret`.
2. Pada Data, pilih `default -> public`, lalu track tabel `users` bila belum di-track.
3. Pada API -> GraphiQL, gunakan query berikut.

```graphql
query GetUsers {
  users(order_by: { id: asc }) {
    id
    name
    email
    created_at
    updated_at
  }
}
```

"Dengan query ini, data yang dibuat dari REST User Service dapat dibaca melalui Hasura GraphQL. Ini menunjukkan penggunaan REST dan Hasura GraphQL pada User Service."

### Zahwa Aisya Nabila - Product Service dan GraphQL Lighthouse

"Saya menangani Product Service. Service ini mengelola katalog produk dan memiliki REST API serta GraphQL manual berbasis Laravel Lighthouse. Product Service berjalan pada port 8003 dan menyimpan data menggunakan SQLite."

REST endpoint:

```text
POST http://localhost:8003/api/products
GET  http://localhost:8003/api/products
GET  http://localhost:8003/api/products/{id}
```

Endpoint GraphQL manual:

```text
POST http://localhost:8003/graphql
```

Query GraphQL:

```graphql
{
  products {
    id
    name
    price
    stock
  }
}
```

Mutation GraphQL:

```graphql
mutation {
  createProduct(name: "Sample Product", price: 250000, stock: 10) {
    id
    name
    price
    stock
  }
}
```

Demo: pada Postman, jalankan `Create product - GraphQL`, lalu `List products - GraphQL` dan `List products - REST`.

"Product Service memenuhi GraphQL yang diimplementasikan langsung pada backend framework melalui Lighthouse, bukan melalui Hasura."

### Karin Zalfa Santoso - Order Service dan Redis Publisher

"Saya menangani Order Service. Service ini membuat dan menampilkan order menggunakan REST API dengan database MySQL. Saat order berhasil dibuat, service menerbitkan event OrderCreated ke Redis channel order_events. Pengiriman event ini bersifat asinkron, sehingga Order Service tidak perlu menunggu Notification Service selesai memproses notifikasi."

REST endpoint:

```text
POST http://localhost:8002/api/orders
GET  http://localhost:8002/api/orders
GET  http://localhost:8002/api/orders/{id}
```

Payload create order:

```json
{
  "user_id": 1,
  "product_id": 1,
  "quantity": 1,
  "total_price": 250000
}
```

Demo: jalankan `Create order and publish OrderCreated`. Respons yang diharapkan adalah HTTP `201` dan data order dengan status `pending`. Lalu jalankan `List orders for user` dan `Get order by ID` memakai ID dari respons create order.

"Order Service adalah publisher pada Redis message broker."

### Alya Fazaristi - Notification Service dan Redis Subscriber

"Saya menangani Notification Service. Service ini memiliki REST API dan database MySQL sendiri. Container notification-event-consumer melakukan subscribe ke Redis channel order_events. Ketika event OrderCreated diterima, consumer otomatis membuat notification untuk user terkait."

REST endpoint:

```text
GET  http://localhost:8004/api/notifications
POST http://localhost:8004/api/notifications/send
```

Demo:

1. Setelah Karin membuat order, jalankan `List notifications from Redis event`.
2. Tampilkan notifikasi dengan `type` bernilai `order_created`.
3. Jalankan `Send notification manually` untuk mendemonstrasikan endpoint REST manual.

"Notification Service adalah subscriber atau consumer Redis. Ini membuktikan komunikasi asynchronous antarservice dari Order Service ke Notification Service."

## 7. Flow Fitur Utama

1. Khaila membuat data user dengan User Service melalui REST.
2. Zahwa membuat produk melalui mutation GraphQL Lighthouse pada Product Service.
3. Karin membuat order dengan `user_id` dan `product_id` yang sudah tersedia.
4. Order Service menyimpan order pada MySQL dan publish event `OrderCreated` ke Redis.
5. Notification consumer menerima event tersebut dan menyimpan notification pada MySQL Notification Service.
6. Alya menampilkan notification melalui REST API.
7. Khaila menunjukkan data user dari PostgreSQL dengan Hasura GraphQL.

## 8. Skenario Error untuk Demo

Gunakan folder `FAIL` pada Postman collection untuk menunjukkan validasi API.

| Service | Skenario | Status yang Diharapkan |
|---|---|---:|
| User | Email duplikat | 400 |
| User | Payload user tidak lengkap | 400 |
| User | User ID tidak ada | 404 |
| Product | Harga atau stok negatif | 422 |
| Product | Product ID tidak ada | 404 |
| Order | Quantity bernilai 0 | 422 |
| Order | Order ID tidak ada | 404 |
| Notification | Payload notification tidak lengkap | 422 |

## 9. Pemetaan ke Rubrik Penilaian

| Aspek Penilaian | Bukti Implementasi |
|---|---|
| GraphQL Implementation | Lighthouse pada Product Service dan Hasura pada User Service |
| Docker Deployment | Docker Compose menjalankan empat service, database masing-masing, Redis, Hasura, dan notification consumer |
| RESTful dan Message Broker | Semua service memiliki endpoint REST; Order publish dan Notification subscribe Redis |
| Dokumentasi dan Arsitektur | Dokumen ini, README, diagram arsitektur, dan collection Postman |
| Presentasi dan Demo | Demo dilakukan sesuai flow pada bagian 7 |

## 10. Checklist Sebelum Presentasi/Pengumpulan

- Jalankan `docker compose ps`; pastikan empat service, Redis, database, dan `notification-event-consumer` berstatus Up.
- Pastikan `user-hasura` berjalan dan tabel `users` sudah di-track.
- Import `postman/ShopEase.postman_collection.json` versi terbaru.
- Jalankan Create user 1 sampai 4, Create product, lalu Create order sebelum demo notification.
- Pastikan ID pada request Order sesuai dengan data yang berhasil dibuat.
- Pastikan `GET /api/orders/{id}` sudah menggunakan container Order Service hasil rebuild terbaru.
- Siapkan screenshot atau rekaman `docker compose ps`, Hasura GraphiQL, request GraphQL Lighthouse, dan flow notification Redis.
- Masukkan link collection Postman yang dibagikan ke laporan.
- Masukkan link GitHub repository ke laporan: `https://github.com/<organisasi-atau-username>/<nama-repository>`.
- Buat laporan PDF berisi deskripsi, diagram, link Postman, link GitHub, fitur, serta flow ini.

## 11. Materi yang Dikumpulkan

1. Laporan PDF proyek.
2. Link collection/dokumentasi Postman tiap service.
3. Link GitHub repository.
4. Bukti demo Docker, GraphQL Lighthouse, Hasura, REST API, dan Redis event flow.
