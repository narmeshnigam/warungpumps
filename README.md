# Warung Pumps

Static website for Warung Pumps company.

## Setup

1. Install dependencies:
   ```bash
   npm install
   ```
2. Initialize the SQLite database and seed admin users:
   ```bash
   node scripts/init_db.js
   ```
3. Start the development server:
   ```bash
   npm start
   ```

The script will create a default admin account (`admin@warung.com` / `admin123`) and a test user (`nigamnarmesh@gmail.com` / `1234567890`).
