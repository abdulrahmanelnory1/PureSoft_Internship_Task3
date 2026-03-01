# E‑Commerce (Simple PHP)

Brief, self-contained e-commerce demo written in plain PHP and MySQL.

**Characteristics**
- Simple PHP pages (no framework).
- MySQL database stored in `e-commerce_db.sql`.
- File-based structure suitable for XAMPP / local Apache + MySQL.
- Minimal styling and example product/images folder for demonstration.

**Project Structure**
- `add_product.php` — form to add products (admin/demo).
- `products.php` — product listing page.
- `show.php` — single product detail view.
- `cart.php` — shopping cart page.
- `checkout.php` — checkout flow placeholder.
- `Auth/` — authentication pages (`login.php`, `register.php`, `logout.php`).
- `config/database.php` — database connection configuration.
- `images/` — example image assets.
- `e-commerce_db.sql` — SQL dump for creating/importing the database.

**Setup (local, XAMPP)**
1. Ensure XAMPP (Apache + MySQL) is installed and running.
2. Place the project folder in XAMPP's `htdocs` (e.g., `C:\xampp\htdocs\e-commerce`).
3. Import the database using phpMyAdmin or the command line:

```bash
mysql -u root -p < e-commerce_db.sql
```

(If using XAMPP defaults, `root` often has no password — press Enter when prompted.)

4. Open the site in your browser: `http://localhost/e-commerce/`.

**Usage Notes**
- Add products with `add_product.php` and view them through `products.php`.
- Use `cart.php` to add/remove items and `checkout.php` as the final step (demo).
- Authentication pages live under the `Auth/` folder.

**Security & Improvements (suggested)**
- Use prepared statements (PDO or mysqli prepared) to prevent SQL injection.
- Validate and sanitize all user inputs and file uploads.
- Add CSRF protection on forms and authentication hardening.
- Move DB credentials out of webroot or use environment variables.

**Where to start for development**
- Review `config/database.php` to confirm DB credentials.
- Import `e-commerce_db.sql` then open `products.php` to verify data.

**Contact / License**
This is a small demo project — adapt freely for learning purposes.
# E-Commerce Sample

This workspace contains a basic PHP e-commerce skeleton with:

- `config/database.php` - PDO connection to MySQL.
- `index.php` - homepage listing categories from `categories` table (links to subcategories)
- `subcategories.php` - new page showing a category's sub‑categories and routing to products
- `products.php` - product listing filtered by category.
- `cart.php` - simple session-based shopping cart.
- Authentication under `Auth/` (login/register/logout).

Populate some categories, sub‑categories, and products before using the homepage.
Products must now be assigned to a sub_category (see schema above).

## Usage

1. Place the project in your web root (e.g. `htdocs/e-commerce`).
2. Update `config/database.php` credentials.
3. Create the tables and seed data.
4. Visit `/e-commerce/index.php` in your browser.
"# PureSoft_Internship_Task3" 
