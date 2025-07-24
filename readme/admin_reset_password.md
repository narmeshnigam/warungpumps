**Page Title:** Reset Password Page

**Page Purpose:** Allow admins or editors to securely reset their password using a time-sensitive token sent via email. Ensures account recovery without exposing credentials.

**Intended Admin User:**

- Admin
- Editor

---

**Detailed Content Outline (With Precise UI Instructions)**

**Section 1: Reset Password Form (Centered Card)**

- **UI Instructions:**

  - Centered white card with box-shadow and soft corners
  - Token read from URL and passed to API on submission
  - Heading (H2): "Reset Your Password"
  - Subheading: "Enter your new password below"

- **Form Fields:**

  - **New Password** (type: password)
  - **Confirm New Password** (type: password)

- **Buttons:**

  - Primary: **Reset Password** (full-width)
  - Secondary (optional): **Back to Login** (text link)

- **Visuals:**

  - Icon: Lock reset symbol
  - Button uses accent orange with hover effect

- **Styling:**

  - Fonts: Poppins (heading), Open Sans (form), Montserrat (button)
  - Colors: Accent blue #0A3D62, Orange #F76C1C, Muted text #4A4A4A

---

**Backend API Endpoints**

**1. POST /api/admin/reset-password**

- **Purpose:** Reset user password using token from email
- **Payload:**

```json
{
  "token": "uuid-token",
  "newPassword": "NewSecret123"
}
```

- **Response (success):**

```json
{
  "success": true,
  "message": "Password updated successfully"
}
```

- **Response (failure):**

```json
{
  "success": false,
  "message": "Invalid or expired token"
}
```

---

**Database Schema / SQL Notes**

- **Table:** `admin_users`
- **Operation:** Match `reset_token` and `reset_token_expiry` >= current timestamp
- **Query Example:**

```sql
SELECT * FROM admin_users WHERE reset_token = ? AND reset_token_expiry >= NOW();
```

- If valid, update password and clear token fields:

```sql
UPDATE admin_users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?;
```

---

**Client-Side JS Interactions**

- Read token from URL (`/admin/reset-password/:token`)
- On submit:
  - Validate both fields match and meet criteria
  - Disable button, show spinner
  - POST to `/api/admin/reset-password`
  - On success: Redirect to login with success toast
  - On failure: Show error below form

---

**Access Control**

- Public page
- Redirect to login if token is missing or invalid (based on response)

---

**Visual Guidelines & Assets**

- Match login page style (spacing, input design, button hover scale)
- Inline error messages for mismatch/invalid fields

---

**UX & Interaction Notes**

- Show error if passwords don’t match
- Success toast before redirecting to login
- Input must hide characters (type: password)
- Responsive layout on all screen sizes

---

**Page Metadata (if applicable)**

- Meta Title: Reset Password | Warung Admin
- Meta Description: Set a new password for your Warung Pumps admin account.

---

**CTA & Admin Actions Summary**

- **Reset Password** updates password and redirects to login
- **Back to Login** returns user to login screen

