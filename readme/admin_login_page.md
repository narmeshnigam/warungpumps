**Page Title:** Admin Login Page

**Page Purpose:** To authenticate administrators and editors before granting access to the admin dashboard. This page ensures only authorized users can manage website content, inquiries, and settings.

**Intended Admin User:**

- Admin
- Editor

---

**Detailed Content Outline (With Precise UI Instructions)**

**Section 1: Login Form (Centered Box)**

- **UI Instructions:**

  - Centered card on page with box-shadow and border-radius.
  - Padded form with light background (#FFFFFF) and soft shadow.
  - Heading (H2): "Admin Login"
  - Subheading (optional): "Enter your credentials to continue."

- **Form Fields:**

  - **Email Address** (type: email)
  - **Password** (type: password)
  - **Remember Me** checkbox (optional)

- **Buttons:**

  - Primary: **Login** (full-width button)
  - Secondary (optional): **Forgot Password?** link styled inline

- **Visuals:**

  - Industrial line icon (lock or shield)
  - Button uses primary orange (#F76C1C), hover scale 1.05

- **Styling:**

  - Fonts: Poppins (heading), Open Sans (form), Montserrat (button)
  - Colors: Text muted #4A4A4A, Border #0A3D62, Button text #FFFFFF

---

**Backend API Endpoints**

**1. POST /api/admin/login**

- **Purpose:** Authenticate user and generate token (JWT or session)
- **Payload:**

```json
{
  "email": "admin@example.com",
  "password": "secretpass"
}
```

- **Response:**

```json
{
  "success": true,
  "token": "jwt_token_here",
  "role": "admin"
}
```

- **Failure Response:**

```json
{
  "success": false,
  "message": "Invalid credentials."
}
```

- **Authentication:** None required to access (public endpoint)

---

**Database Schema / SQL Notes**

- **Table:** `admin_users`
- **Fields:** id, email, password\_hash, role, created\_at, last\_login
- **Login Query Example:**

```sql
SELECT * FROM admin_users WHERE email = ? LIMIT 1;
```

- Passwords should be stored using `bcrypt` hashing

---

**Client-Side JS Interactions**

- On form submit:
  - Disable login button, show spinner
  - Call `/api/admin/login` with entered credentials
  - On success: Store token in `localStorage` or `cookie` and redirect to `/admin/dashboard`
  - On failure: Show inline error message below password field

---

**Access Control**

- Public page, no login required
- Redirect to `/admin/dashboard` if token already exists

---

**Visual Guidelines & Assets**

- Use same border-radius, spacing, and box-shadow style as main site cards
- Submit button animation: scale and brightness on hover
- Consistent with brand fonts and colors

---

**UX & Interaction Notes**

- Input errors show below field in red (#E74C3C)
- Smooth transition on submit with form loading state
- Optional: toast on login success

---

**Page Metadata (if applicable)**

- Meta Title: Warung Admin Login
- Meta Description: Secure login for Warung Pumps admin panel.

---

**CTA & Admin Actions Summary**

- **Login** button triggers POST to login API
- **Redirect** to dashboard on success
- **Error** displayed on incorrect credentials

