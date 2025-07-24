const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3000;
const SECRET = process.env.JWT_SECRET || 'warung_secret';

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use('/admin', express.static(path.join(__dirname, 'admin')));
app.use('/', express.static(__dirname));

// ensure uploads directory
const uploadsDir = path.join(__dirname, 'uploads');
if (!fs.existsSync(uploadsDir)) fs.mkdirSync(uploadsDir);
const upload = multer({ dest: uploadsDir });

const db = new sqlite3.Database(path.join(__dirname, 'database.db'));

db.serialize(() => {
  db.run(`CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT UNIQUE,
    password TEXT
  )`);
  db.run(`CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT
  )`);
  db.run(`CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    description TEXT,
    category_id INTEGER,
    warranty_months INTEGER,
    support_type TEXT,
    image TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
  )`);
  db.run(`CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
  )`);
  db.run(`CREATE TABLE IF NOT EXISTS activities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT,
    message TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
  )`);
  db.run(`CREATE TABLE IF NOT EXISTS reset_tokens (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER,
    token TEXT,
    expires_at DATETIME
  )`);

  // Insert default admin
  db.get('SELECT COUNT(*) as count FROM admins', (err, row) => {
    if (!row.count) {
      const hash = bcrypt.hashSync('admin123', 10);
      db.run('INSERT INTO admins(email, password) VALUES (?, ?)', ['admin@warung.com', hash]);
    }
  });
});

function authenticate(req, res, next) {
  const header = req.headers['authorization'];
  if (!header) return res.status(401).json({ message: 'Missing token' });
  const token = header.split(' ')[1];
  try {
    req.user = jwt.verify(token, SECRET);
    next();
  } catch (_) {
    res.status(401).json({ message: 'Invalid token' });
  }
}

app.post('/api/admin/login', (req, res) => {
  const { email, password } = req.body;
  db.get('SELECT * FROM admins WHERE email = ?', [email], (err, row) => {
    if (!row) return res.json({ success: false, message: 'Invalid credentials' });
    if (!bcrypt.compareSync(password, row.password)) {
      return res.json({ success: false, message: 'Invalid credentials' });
    }
    const token = jwt.sign({ id: row.id, email: row.email }, SECRET);
    res.json({ success: true, token });
  });
});

app.post('/api/admin/forgot-password', (req, res) => {
  const { email } = req.body;
  if (!email) return res.json({ success: false, message: 'Email required' });
  db.get('SELECT id FROM admins WHERE email=?', [email], (err, row) => {
    if (!row) return res.json({ success: false, message: 'Email not found' });
    const token = require('crypto').randomBytes(20).toString('hex');
    const expires = Date.now() + 3600 * 1000;
    db.run('INSERT INTO reset_tokens(admin_id, token, expires_at) VALUES(?,?,?)', [row.id, token, expires], (err2) => {
      if (err2) return res.status(500).json({ success: false, message: 'DB error' });
      res.json({ success: true, message: 'Reset link generated', token });
    });
  });
});

app.post('/api/admin/reset-password', (req, res) => {
  const { token, newPassword } = req.body;
  if (!token || !newPassword) return res.json({ success: false, message: 'Invalid request' });
  db.get('SELECT admin_id, expires_at FROM reset_tokens WHERE token=?', [token], (err, row) => {
    if (!row || row.expires_at < Date.now()) {
      return res.json({ success: false, message: 'Token expired or invalid' });
    }
    const hash = bcrypt.hashSync(newPassword, 10);
    db.run('UPDATE admins SET password=? WHERE id=?', [hash, row.admin_id], (err2) => {
      if (err2) return res.status(500).json({ success: false, message: 'DB error' });
      db.run('DELETE FROM reset_tokens WHERE token=?', [token]);
      db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['password_reset', 'Admin password reset']);
      res.json({ success: true, message: 'Password reset successful' });
    });
  });
});

app.get('/api/admin/dashboard/overview', authenticate, (req, res) => {
  db.serialize(() => {
    db.get('SELECT COUNT(*) as c FROM products', (e1, prod) => {
      db.get('SELECT COUNT(*) as c FROM categories', (e2, cat) => {
        res.json({
          totalProducts: prod.c,
          totalInquiries: 0,
          categories: cat.c,
          lastUpdated: Date.now()
        });
      });
    });
  });
});

app.get('/api/admin/dashboard/recent-activity', authenticate, (req, res) => {
  db.all('SELECT type, message, timestamp FROM activities ORDER BY timestamp DESC LIMIT 5', (err, rows) => {
    res.json(rows || []);
  });
});

app.get('/api/categories', authenticate, (req, res) => {
  db.all('SELECT id, name FROM categories', (err, rows) => {
    res.json(rows || []);
  });
});

app.post('/api/categories', authenticate, (req, res) => {
  const { name } = req.body;
  if (!name) return res.status(400).json({ message: 'Name required' });
  db.run('INSERT INTO categories(name) VALUES(?)', [name], function(err) {
    if (err) return res.status(500).json({ message: 'DB error' });
    db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['category_add', `Added category ${name}`]);
    res.json({ id: this.lastID, name });
  });
});

app.post('/api/products/add', authenticate, upload.single('image'), (req, res) => {
  const { name, description, category_id, warranty_months, support_type } = req.body;
  const img = req.file ? req.file.filename : null;
  if (!name || !category_id) return res.status(400).json({ message: 'Missing fields' });
  db.run(
    `INSERT INTO products(name, description, category_id, warranty_months, support_type, image) VALUES(?,?,?,?,?,?)`,
    [name, description, category_id, warranty_months, support_type, img],
    function(err) {
      if (err) return res.status(500).json({ message: 'DB error' });
      db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['product_add', `Added product ${name}`]);
      res.json({ success: true, id: this.lastID });
    }
  );
});

app.get('/api/products', (req, res) => {
  const { search = '', category = '', page = 1 } = req.query;
  const pageSize = 10;
  const params = [];
  let base = 'FROM products LEFT JOIN categories ON products.category_id = categories.id';
  let where = [];
  if (search) {
    where.push('products.name LIKE ?');
    params.push(`%${search}%`);
  }
  if (category) {
    where.push('category_id = ?');
    params.push(category);
  }
  const whereClause = where.length ? 'WHERE ' + where.join(' AND ') : '';
  db.get(`SELECT COUNT(*) as c ${base} ${whereClause}`, params, (err, countRow) => {
    const offset = (page - 1) * pageSize;
    const queryParams = params.slice();
    queryParams.push(pageSize, offset);
    db.all(
      `SELECT products.id, products.name, products.description, products.warranty_months, products.support_type, products.image, categories.name as category ${base} ${whereClause} LIMIT ? OFFSET ?`,
      queryParams,
      (e2, rows) => {
        const products = (rows || []).map(r => ({
          id: r.id,
          name: r.name,
          description: r.description,
          category: r.category,
          warranty: r.warranty_months,
          support: r.support_type,
          image: r.image ? '/uploads/' + r.image : null,
          image_url: r.image ? '/uploads/' + r.image : null
        }));
        res.json({ products, total: countRow ? countRow.c : 0 });
      }
    );
  });
});

app.get('/api/products/:id', (req, res) => {
  db.get(
    `SELECT id, name, description, category_id, warranty_months, support_type, image FROM products WHERE id=?`,
    [req.params.id],
    (err, row) => {
      if (!row) return res.status(404).json({ message: 'Not found' });
      res.json({
        id: row.id,
        name: row.name,
        description: row.description,
        category_id: row.category_id,
        warranty_months: row.warranty_months,
        support_type: row.support_type,
        image_url: row.image ? '/uploads/' + row.image : null
      });
    }
  );
});

app.put('/api/products/:id', authenticate, upload.single('image'), (req, res) => {
  const { name, description, category_id, warranty_months, support_type } = req.body;
  const img = req.file ? req.file.filename : null;
  db.get('SELECT image FROM products WHERE id=?', [req.params.id], (err, row) => {
    const imageToUse = img || (row ? row.image : null);
    db.run(
      `UPDATE products SET name=?, description=?, category_id=?, warranty_months=?, support_type=?, image=? WHERE id=?`,
      [name, description, category_id, warranty_months, support_type, imageToUse, req.params.id],
      function (err2) {
        if (err2) return res.status(500).json({ message: 'DB error' });
        db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['product_update', `Updated product ${name}`]);
        res.json({ success: true });
      }
    );
  });
});

app.delete('/api/products/:id', authenticate, (req, res) => {
  db.run('DELETE FROM products WHERE id=?', [req.params.id], function (err) {
    if (err) return res.status(500).json({ success: false });
    db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['product_delete', `Deleted product ${req.params.id}`]);
    res.json({ success: true });
  });
});

app.get('/api/settings/site', authenticate, (req, res) => {
  db.all('SELECT key, value FROM settings', (err, rows) => {
    const settings = {};
    (rows || []).forEach(r => settings[r.key] = r.value);
    res.json(settings);
  });
});

app.post('/api/settings/site', authenticate, (req, res) => {
  const updates = req.body || {};
  db.serialize(() => {
    for (const key of Object.keys(updates)) {
      db.run('INSERT INTO settings(key, value) VALUES(?, ?) ON CONFLICT(key) DO UPDATE SET value=excluded.value', [key, updates[key]]);
    }
    db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['settings_update', 'Updated site settings']);
    res.json({ success: true });
  });
});

app.post('/api/settings/change-password', authenticate, (req, res) => {
  const { oldPassword, newPassword } = req.body;
  db.get('SELECT * FROM admins WHERE id = ?', [req.user.id], (err, row) => {
    if (!row || !bcrypt.compareSync(oldPassword, row.password)) {
      return res.json({ success: false, message: 'Invalid old password' });
    }
    const hash = bcrypt.hashSync(newPassword, 10);
    db.run('UPDATE admins SET password=? WHERE id=?', [hash, req.user.id]);
    db.run('INSERT INTO activities(type, message) VALUES(?, ?)', ['password_change', `Admin changed password`]);
    res.json({ success: true });
  });
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
