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
    db.run('INSERT OR IGNORE INTO admins(email, password) VALUES (?, ?)', ['nigamnarmesh@gmail.com', testHash]);
  });

  // seed categories
  db.get('SELECT COUNT(*) as count FROM categories', (err, row) => {
    if (!row.count) {
      const stmt = db.prepare('INSERT INTO categories(name) VALUES (?)');
      ['Submersible Pumps', 'Tubewell Pumps', 'Openwell Pumps'].forEach(n => stmt.run(n));
      stmt.finalize();
    }
  });

  // seed products
  db.get('SELECT COUNT(*) as count FROM products', (err, row) => {
    if (!row.count) {
      db.all('SELECT id, name FROM categories', (e, cats) => {
        const ids = {};
        (cats || []).forEach(c => ids[c.name] = c.id);
        const stmt = db.prepare('INSERT INTO products(name, description, category_id, warranty_months, support_type, image) VALUES (?,?,?,?,?,?)');
        stmt.run('WRG-1HP-SUB', 'Ideal for 150ft deep borewell', ids['Submersible Pumps'], 12, 'Phone', 'pump1.jpg');
        stmt.run('WRG-2HP-TUBE', 'Heavy-duty tubewell pump', ids['Tubewell Pumps'], 12, 'Phone', 'pump2.jpg');
        stmt.run('WRG-0.75HP-OPEN', 'Best for shallow water lifting', ids['Openwell Pumps'], 12, 'Phone', 'pump3.jpg');
        stmt.finalize(() => {
          console.log('Database initialized with dummy data');
          db.close();
        });
      });
    } else {
      console.log('Database initialized');
      db.close();
    }
  });
});
