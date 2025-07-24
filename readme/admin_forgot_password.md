**Page Title:** Forgot Password Page

**Page Purpose:** Allow admins or editors to initiate a password reset process securely by entering their registered email address. A reset link will be emailed to them to complete the process.

**Intended Admin User:**

- Admin
- Editor

---

**Detailed Content Outline (With Precise UI Instructions)**

**Section 1: Forgot Password Form (Centered Card)**

- **UI Instructions:**

  - Centered container card with soft white background and box-shadow
  - Padding and rounded edges to match the design theme
  - Heading (H2): "Forgot Password"
  - Subheading: "Enter your email to receive a reset link"

- **Form Field:**

  - **Email Address** (type: email)

- **Buttons:**

  - Primary: **Send Reset Link** (full-width)
  - Secondary (optional): **Back to Login** (text link)

- **Visuals:**

  - Icon: Envelope or shield key symbol
  - Use orange button with hover scale 1.05

- **Styling:**

  - Fonts: Poppins (heading), Open Sans (form), Montserrat (button)
  - Colors: Accent blue #0A3D62, Accent orange #F76C1C, Text muted #4A4A4A

---

**Backend API Endpoints**

**1. POST /api/admin/forgot-password**

- **Purpose:** Send a reset link to the user's email if it exists in the system
- **Payload:**

```json
{
  "email": "admin@example.com"
}
```

- **Response (success):**

```json
{
  "success": true,
  "message": "Reset link sent successfully"
}
```

- **Response (failure):**

```json
{
  "success": false,
  "message": "Email not registered"
}
```

**2. POST /api/admin/reset-password** (used in next page)

---

**Database Schema / SQL Notes**

- **Table:** `admin_users`
- **Fields involved:** email, reset\_token, reset\_token\_expiry
- **Token Generation Logic:**
  - Generate secure UUID token
  - Store `reset_token` and `expiry` (15–30 min) in DB
  - Email reset link: `/admin/reset-password/:token`

**Query Example:**

```sql
UPDATE admin_users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?;
```

---

**Client-Side JS Interactions**

- On submit:
  - Disable button and show spinner
  - Call `/api/admin/forgot-password`
  - Show success message or inline error
  - Optionally disable form after success

---

**Access Control**

- Public page
- Redirect to `/admin/dashboard` if already logged in

---

**Visual Guidelines & Assets**

- Follow same spacing, font, shadow, and form design as login page
- Responsive layout with mobile-first stacking

---

**UX & Interaction Notes**

- Show toast/alert after form submit indicating email sent
- Error text in red if email invalid or not found
- Use clean transitions and field validations

---

**Page Metadata (if applicable)**

- Meta Title: Forgot Admin Password | Warung Pumps
- Meta Description: Enter your email to reset your Warung admin panel password.

---

**CTA & Admin Actions Summary**

- **Send Reset Link** triggers email with secure token
- **Redirect** user to reset page via email link
- **Back to Login** returns to login screen

