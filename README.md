# 🛍️ Velvet Vogue — Online Fashion Store Website

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white"/>
  <img src="https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black"/>
  <img src="https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=apache&logoColor=white"/>
</p>

A full-featured, responsive **online fashion store** built with PHP and MySQL. Velvet Vogue offers a complete e-commerce experience — from product browsing and cart management to order tracking and an admin dashboard — all wrapped in a premium, modern UI.

---

## ✨ Features

### 🛒 Customer Side
- **Home Page** — Hero section, featured products, trending items, and category showcase
- **Product Listing** — Filter by category, search, and sort products
- **Product Detail** — Image gallery, size/color selection, reviews & ratings
- **Shopping Cart** — Add/remove items, update quantities
- **Checkout** — Order placement with shipping details
- **Wishlist** — Save favourite products for later
- **Order History** — Track current and past orders
- **User Account** — Profile management, password change
- **Authentication** — Register, Login, Forgot Password

### 🔧 Admin Panel
| Module | Capabilities |
|---|---|
| Dashboard | Sales overview, recent orders, quick stats |
| Products | Add / Edit / Delete products with image upload |
| Categories | Manage product categories |
| Orders | View and update order statuses |
| Users | Manage registered customers |
| Contacts | View customer enquiry messages |

---

## 🗂️ Project Structure

```
Velvet Vogue Online Fashion Store Website/
├── index.php                  # Home page
├── products.php               # Product listing
├── product-detail.php         # Single product view
├── cart.php                   # Shopping cart
├── checkout.php               # Order checkout
├── wishlist.php               # Wishlist
├── orders.php                 # Order history
├── account.php                # User profile
├── login.php / register.php   # Authentication
├── about.php / contact.php    # Informational pages
│
├── admin/                     # Admin dashboard
│   ├── index.php              # Dashboard
│   ├── products.php           # Product management
│   ├── categories.php         # Category management
│   ├── orders.php             # Order management
│   ├── users.php              # User management
│   ├── contacts.php           # Contact messages
│   ├── css/                   # Admin-specific styles
│   ├── js/                    # Admin-specific scripts
│   └── includes/              # Admin header/footer
│
├── config/
│   └── db.php                 # Database configuration
│
├── includes/
│   ├── header.php             # Global header & nav
│   └── footer.php             # Global footer
│
├── php/                       # Backend PHP handlers
│   ├── cart.php
│   ├── login.php / register.php / logout.php
│   ├── contact.php
│   ├── review.php
│   ├── search.php
│   ├── user_detail_handler.php
│   └── wishlist.php
│
├── css/                       # Page-specific stylesheets
├── js/                        # Page-specific JavaScript
├── uploads/                   # Product image uploads
└── database/
    └── velvet_vogue.sql       # Full database schema & seed data
```

---

## 🚀 Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (PHP 8.x + MySQL 8.x)
- A modern web browser

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/angiedazun/Velvet-Vogue-Online-Fashion-Store-Website.git
```

**2. Move to XAMPP's htdocs folder**
```
C:\xampp\htdocs\Velvet Vogue Online Fashion Store Website\
```

**3. Import the database**
- Start **XAMPP** and enable **Apache** and **MySQL**
- Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Create a new database named `velvet_vogue`
- Click **Import** and select `database/velvet_vogue.sql`

**4. Configure the database connection**

Create/update `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // your MySQL password
define('DB_NAME', 'velvet_vogue');
define('SITE_URL', 'http://localhost/Velvet%20Vogue%20Online%20Fashion%20Store%20Website');
define('CURRENCY_SYMBOL', 'Rs.');
```

**5. Run the project**

Open your browser and navigate to:
```
http://localhost/Velvet%20Vogue%20Online%20Fashion%20Store%20Website/
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.x |
| Database | MySQL 8.x / MySQLi |
| Frontend | HTML5, CSS3, JavaScript (ES6) |
| UI Framework | Bootstrap 5 |
| Icons | Font Awesome |
| Local Server | XAMPP |

---

## 👤 Default Admin Access

After importing the database, log in to the admin panel at `/admin/`:
```
URL:      http://localhost/Velvet%20Vogue%20Online%20Fashion%20Store%20Website/admin/
```
*(Check the imported SQL file for default admin credentials or register a new account and promote it to admin in the `users` table.)*

---

## 📸 Screenshots

> Add screenshots of your project here by placing images in a `/screenshots` folder and referencing them below.

| Home Page | Products | Admin Dashboard |
|---|---|---|
| *(screenshot)* | *(screenshot)* | *(screenshot)* |

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

## 🙋‍♀️ Author

**Angie Dazun**  
GitHub: [@angiedazun](https://github.com/angiedazun)

---

> ⭐ If you found this project helpful, please give it a star!
