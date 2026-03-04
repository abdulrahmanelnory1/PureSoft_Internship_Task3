# E‑Commerce (Simple PHP)

Brief, self-contained e-commerce demo written in plain PHP and MySQL.

**Characteristics**
- Simple PHP pages (no framework).
- PDO/MySQL connection in `config/database.php`.
- Session-based cart and minimal authentication under `Auth/`.
- Database dump in `e-commerce_db.sql` for quick import.
- File-based structure intended for XAMPP / local Apache + MySQL.

**Project Structure**
- `index.php` — homepage (categories overview).
- `subcategories.php` — list subcategories for a category.
- `products.php` — product listing (category/subcategory filtered).
- `show.php` — single product detail view.
- `add_product.php` — admin/demo form to add products (with image upload).
- `cart.php` — shopping cart (session-based).
- `checkout.php` — checkout placeholder/flow.
- `Auth/` — authentication pages (`login.php`, `register.php`, `logout.php`).
- `config/database.php` — database connection configuration (PDO).
- `images/` — product image assets.
- `e-commerce_db.sql` — SQL dump for creating/importing the database and schema.

**Contributions — What I implemented**
- Implemented product management: add products and image uploads via `add_product.php`.
- Built product listing and detail views: `products.php` and `show.php`.
- Implemented category → subcategory → product navigation (`index.php`, `subcategories.php`).
- Implemented a session-based cart (`cart.php`) with add/remove functionality.
- Added a simple checkout placeholder flow in `checkout.php`.
- Added authentication pages under `Auth/` (`login.php`, `register.php`, `logout.php`).
- Created database schema and export (`e-commerce_db.sql`) and configured DB connection in `config/database.php`.
- Included sample images in `images/` and basic frontend templates for demonstration.

**Usage Notes**
- Add or edit products with `add_product.php` (admin/demo).
- Browse categories from `index.php`, drill into subcategories and products.
- Use `cart.php` to view items in the session cart and proceed to `checkout.php`.
- Login/register through the `Auth/` pages to test protected flows.
