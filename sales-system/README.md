SalesTrack – Sales Tracking System

ITEL 203 – Web Systems and Technologies | Group Performance Task #2
TESTINGgit add .
Members:
1. Lebrone Aramil
2. David Esteban
3. Julian Malolos

Features
- Full CRUD for Sales, Products, and Customers
- Login / Logout (session-based authentication)
- Search & Filter (by customer, product, payment status)
- Dashboard with Charts (monthly revenue, order status, top products)
- Bootstrap 5 responsive UI
- Modular PHP file structure (config, auth, navbar)
- MySQL database with foreign key relationships

Tech Stack
PHP · MySQL · HTML/CSS · Bootstrap 5 · Chart.js · JavaScript · XAMPP

File Structure:

sales-system/
├── config.php          DB connection + session start
├── auth_check.php      Login guard (include on protected pages)
├── navbar.php          Shared navigation bar
├── login.php           Login page
├── logout.php          Session destroy
├── index.php           Sales CRUD (main page)
├── products.php        Products CRUD
├── customers.php       Customers CRUD
├── dashboard.php       Reports & analytics (BONUS)
├── about-project.php   About the system
├── developers.php      About the team
├── database.sql        Full DB schema + sample data
└── assets/
    └── styles.css      Global shared styles
