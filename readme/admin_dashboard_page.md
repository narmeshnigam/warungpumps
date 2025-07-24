**Page Title:** Admin Dashboard Page

**Page Purpose:** Display key insights and provide navigation to core content management tools. Acts as the central control panel for Warung Pumps' website backend.

**Intended Admin User:**

- Admin
- Editor (restricted modules)

---

**Detailed Content Outline (With Precise UI Instructions)**

**Section 1: Dashboard Header & Greeting**

- **UI Instructions:**
  - Header with admin name and last login timestamp
  - Welcome message: "Welcome back, [Admin Name]"
  - Subtext (e.g., "Here’s an overview of current website activity")

**Section 2: Quick Metrics Cards (4 Cards)**

- **Metrics to Show:**
  - Total Products
  - Total Inquiries
  - Categories Managed
  - Last Updated (Content Timestamp)
- **UI Instructions:**
  - Grid of 4 cards (2x2 on mobile)
  - Icon + Number + Label
  - Soft hover effect with slight scale-up and shadow

**Section 3: Recent Activity Feed**

- **Items to List:**
  - Recently added or edited products
  - New inquiries received
- **UI Instructions:**
  - List format with timestamps
  - Icon (edit/add/envelope) + action + timestamp

**Section 4: Quick Actions Panel**

- **Buttons:**
  - Add New Product
  - View All Inquiries
  - Manage Categories
- **Styling:**
  - Use primary orange CTA style
  - Icons with labels inside button

---

**Backend API Endpoints**

**1. GET /api/admin/dashboard/overview**

- **Purpose:** Fetch all top-level metrics
- **Response:**

```json
{
  "totalProducts": 56,
  "totalInquiries": 128,
  "categories": 4,
  "lastUpdated": "2025-07-24T18:30:00Z"
}
```

**2. GET /api/admin/dashboard/recent-activity**

- **Purpose:** Fetch list of recent admin actions (add/edit products, new inquiries)
- **Response:**

```json
[
  { "type": "product_add", "message": "Added X200 Pump", "timestamp": "2025-07-24T10:00:00Z" },
  { "type": "inquiry", "message": "New inquiry from Mahesh", "timestamp": "2025-07-23T20:15:00Z" }
]
```

---

**Database Schema / SQL Notes**

- **Tables Used:**
  - `products` → COUNT
  - `inquiries` → COUNT
  - `categories` → COUNT
  - `admin_activity_log` (optional) → RECENT EVENTS

**Sample Queries:**

```sql
SELECT COUNT(*) FROM products;
SELECT COUNT(*) FROM inquiries;
SELECT COUNT(*) FROM product_categories;
SELECT * FROM admin_activity_log ORDER BY timestamp DESC LIMIT 5;
```

---

**Client-Side JS Interactions**

- On page load:
  - Fetch metrics and render to cards
  - Fetch activity feed
  - Handle spinner loading states

---

**Access Control**

- Requires valid admin token (JWT/session)
- Redirect to login if not authenticated

---

**Visual Guidelines & Assets**

- Use card styling and layout as on public site (shadow, border-radius)
- Metric icon style: clean linear icon (box, envelope, pencil, clock)
- Consistent padding and responsive grid for cards

---

**UX & Interaction Notes**

- Spinner shown until API data fetched
- Error toast if fetch fails
- Action buttons visible only based on role (e.g., Editor can’t see Category Management)

---

**Page Metadata (if applicable)**

- Meta Title: Admin Dashboard | Warung Pumps
- Meta Description: View quick stats, recent activity, and access tools to manage the Warung Pumps website.

---

**CTA & Admin Actions Summary**

- Add Product → link to /admin/products/add
- View Inquiries → link to /admin/inquiries
- Manage Categories → link to /admin/products/categories

