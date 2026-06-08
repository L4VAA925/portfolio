# E-Trgovina — Web aplikacija za upravljanje online prodajom

Diplomski rad — Software Programming  
Tehnologije: **PHP**, **MySQL**, **HTML**, **CSS**, **JavaScript**

---

## Opis projekta

E-Trgovina je web aplikacija koja omogućava:

- **Kupcima:** registraciju, prijavu, pregled proizvoda, korpu, naručivanje i historiju narudžbi
- **Administratorima:** upravljanje proizvodima, kategorijama i narudžbama

Aplikacija je namjerno jednostavna — pogodna za prezentaciju i odbranu diplomskog rada.

---

## Struktura foldera

```
ecommerce/
├── admin/                  # Administratorski modul
├── assets/css, js/         # Stilovi i JavaScript
├── auth/                   # Prijava, registracija, odjava
├── cart/                   # Korpa
├── config/database.php     # Konfiguracija baze
├── includes/               # Zajednički PHP fajlovi
├── install/setup.php       # Kreiranje admin naloga
├── orders/                 # Checkout i historija
├── sql/database.sql        # SQL skripta
├── index.php               # Katalog
└── product.php             # Detalji proizvoda
```

---

## Instalacija

1. Kopirajte folder `ecommerce` u `htdocs/` (XAMPP) ili `www/` (WAMP)
2. Pokrenite **Apache** i **MySQL**
3. Importujte `sql/database.sql` u phpMyAdmin
4. Provjerite `config/database.php` (host, user, lozinka, BASE_URL)
5. Otvorite `http://localhost/ecommerce/install/setup.php` za admin nalog
6. Otvorite `http://localhost/ecommerce/`

### Admin podaci (nakon setup.php)

| E-mail | Lozinka |
|--------|---------|
| admin@shop.local | Admin123! |

---

## Baza podataka

6 tabela: `users`, `categories`, `products`, `cart_items`, `orders`, `order_items`

Slike proizvoda se unose kao **URL adresa** (npr. `https://picsum.photos/seed/proizvod/400/300`).

---

## Test scenariji

1. Registracija i prijava kupca
2. Pregled kataloga i filtriranje po kategoriji
3. Dodavanje u korpu i potvrda narudžbe
4. Pregled historije narudžbi
5. Admin — CRUD proizvoda i kategorija
6. Admin — promjena statusa narudžbe

---

## Autor

Software Programming student — Diplomski rad
