# 🛡️ SafeSphere — Cybersecurity Awareness Training Platform

SafeSphere is an interactive cybersecurity training platform that teaches users to recognize and defend against modern digital threats including phishing, UPI fraud, OTP scams, social engineering, deepfakes, and network vulnerabilities.

---

## ✅ Prerequisites

Before setting up, make sure you have:
- **XAMPP** (v7.4+ recommended) → [Download here](https://www.apachefriends.org/)
- **Git** → [Download here](https://git-scm.com/)
- A browser (Chrome / Firefox recommended)

---

## 🚀 Setup Instructions (for every team member)

### Step 1 — Clone the repository

```bash
git clone https://github.com/YOUR-USERNAME/safesphere-phase1.git
```

Move the cloned folder into your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\safesphere-phase1\
```

> **Windows tip:** If you cloned it somewhere else, just move/copy the entire folder into `C:\xampp\htdocs\`.

---

### Step 2 — Start XAMPP services

Open **XAMPP Control Panel** and start:
- ✅ **Apache**
- ✅ **MySQL**

---

### Step 3 — Create the database

1. Open your browser and go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **"New"** in the left sidebar
3. Create a database named exactly: `safesphere`
4. Set collation to: `utf8mb4_unicode_ci`
5. Click **Create**

---

### Step 4 — Import the database schema

Still in phpMyAdmin:
1. Click on the `safesphere` database (left sidebar)
2. Click the **"Import"** tab
3. Click **"Choose File"** and select: `db/schema.sql`
4. Click **"Go"** to import

Then repeat with the migration files **in order**:
- `db/migrate_phase2.sql`
- `db/migrate_phase3.sql`

---

### Step 5 — Configure your database connection

```bash
# From inside the project folder:
cp config/db.example.php config/db.php
```

> **Windows:** Copy the file manually — right-click `config/db.example.php` → Copy → Paste in the same `config/` folder → Rename to `db.php`

Open `config/db.php` and verify the settings:

```php
define('DB_HOST', 'localhost');   // leave as-is for XAMPP
define('DB_NAME', 'safesphere'); // must match the database you created
define('DB_USER', 'root');        // XAMPP default
define('DB_PASS', '');            // XAMPP default (empty password)
```

> If you set a MySQL root password in XAMPP, enter it in `DB_PASS`.

---

### Step 6 — (Optional) Create an admin account

After registering your first user via the website, open phpMyAdmin and run:

```sql
UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
```

---

### Step 7 — Open the site

Go to: [http://localhost/safesphere-phase1/](http://localhost/safesphere-phase1/)

You should see the SafeSphere homepage. Register an account and start training! 🎉

---

## 📁 Project Structure

```
safesphere-phase1/
├── assets/
│   ├── css/style.css       # Main stylesheet (dark/light theme)
│   └── js/main.js          # All client-side JavaScript
├── config/
│   ├── db.example.php      # ✅ Safe to commit — template config
│   └── db.php              # ❌ NOT committed — your local credentials
├── db/
│   ├── schema.sql           # Base schema (import first)
│   ├── migrate_phase2.sql   # Phase 2 tables (import second)
│   └── migrate_phase3.sql   # Phase 3 updates (import third)
├── includes/
│   ├── auth.php             # Session & authentication helpers
│   ├── functions.php        # Utility functions
│   ├── header.php           # App shell header (authenticated pages)
│   ├── footer.php           # App shell footer
│   ├── public_nav.php       # Public navigation bar
│   └── public_footer.php    # Public footer
├── modules/                 # Training simulation modules
│   ├── phishing.php
│   ├── upi.php
│   ├── otp.php
│   ├── socialeng.php
│   ├── deepfake.php
│   └── network.php
├── api/                     # AJAX endpoint(s)
│   └── threat_intel.php     # Quick threat scanner API
├── index.php                # Public homepage
├── login.php
├── register.php
├── dashboard.php
├── leaderboard.php
├── certificates.php
├── reports.php
├── forensics.php
├── admin.php
└── README.md
```

---

## ⚠️ Common Issues & Fixes

| Problem | Likely Cause | Fix |
|---|---|---|
| White screen / DB error | `config/db.php` missing or wrong password | Follow Step 5 above |
| Page not found (404) | Folder not in `htdocs` | Move project to `C:\xampp\htdocs\safesphere-phase1` |
| Login doesn't work | Database tables missing | Re-run schema.sql import (Step 4) |
| "Access denied for user 'root'" | Wrong MySQL password | Update `DB_PASS` in `config/db.php` |
| CSS looks broken | Browser cache | Hard-refresh with `Ctrl + Shift + R` |

---

## 👥 Team

- Built with PHP 7.4+, MySQL, vanilla JavaScript — no frameworks or Composer required.
- All assets are self-contained — no npm, no build step needed.

---

## 📄 License

This project is for educational/academic purposes.
