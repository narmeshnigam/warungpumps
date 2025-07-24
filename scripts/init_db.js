const sqlite3 = require('sqlite3').verbose();
const bcrypt = require('bcryptjs');
const path = require('path');

const db = new sqlite3.Database(path.join(__dirname, '..', 'database.db'));

const tables = [
  `CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE,
    password TEXT
  )`,
  `CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT
  )`,
  `CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    description TEXT,
    category_id INTEGER,
    warranty_months INTEGER,
    support_type TEXT,
    image TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )`,
  `CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
  )`,
  `CREATE TABLE IF NOT EXISTS activities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT,
    message TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
  )`,
  `CREATE TABLE IF NOT EXISTS reset_tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER,
    token TEXT,
    expires_at DATETIME
  )`
];

db.serialize(() => {
  tables.forEach(t => db.run(t));

  // default admin
  db.get('SELECT COUNT(*) as count FROM admins', (err, row) => {
    if (!row.count) {
      const hash = bcrypt.hashSync('admin123', 10);
      db.run('INSERT INTO admins(email, password) VALUES (?, ?)', ['admin@warung.com', hash]);
    }
    const testHash = bcrypt.hashSync('1234567890', 10);
    db.run('INSERT OR IGNORE INTO admins(email, password) VALUES (?, ?)', ['nigamnarmesh@gmail.com', testHash], () => {
      console.log('Database initialized');
      db.close();
    });
  });
});
