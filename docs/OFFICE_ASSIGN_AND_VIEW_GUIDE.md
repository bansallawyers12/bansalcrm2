# How to Assign and View Office

This guide explains how to assign users (staff and clients) to offices and where to view office information.

---

## 1. Assign office to a user (staff or client)

### Option A: From the **Client** screen (recommended for clients)

- **Add or change a client’s office**
  1. Go to **Clients Manager** → open the client (detail page).
  2. Click **Edit** (or go to **Edit** from the client menu).
  3. In the form, find the **Office** dropdown (in the same row as Assign To / Status).
  4. Select an office (or leave “Select Office” to clear).
  5. Save. The client is now assigned to that office.

### Option B: From **Users** (for staff, or clients if you prefer)

**Where:** **People** → **Users** (or **User Management**)

- **Create a new user**
  1. Go to **Users** → **Active** (or **Inactive**).
  2. Click **Create User** (or equivalent).
  3. Fill in the form; in the **Office DETAILS** section, choose an **Office** from the dropdown.
  4. Save. The user (staff or client) is now assigned to that office.

- **Change a user’s office**
  1. Go to **Users** → **Active** (or **Inactive**).
  2. Open the user (click name or **Edit User**).
  3. In **Office DETAILS**, change **Office** and save.

**Note:** Clients do not appear in the **Users** list (only staff do). To assign or change a **client’s** office, use **Clients Manager** → open client → **Edit** → **Office** (Option A). Lead → Client conversion sets office from the assigned staff’s office; you can change it later in client edit.

---

## 2. View office on user list, profile, and client

- **User list (Active / Inactive)**  
  The **Office** column shows each user’s office name. The name is a link to that branch’s detail page.

- **User view (profile)**  
  Open a user → the **Offices** section lists all branches and marks the user’s **Primary** office (the one selected in the form).

- **Client detail**  
  Open a client → the **Office** line shows the client’s assigned office (link to branch). If none is set, it shows “-”.

---

## 3. Manage offices (branches)

**Where:** **Branches** (sidebar; may be under **Settings** or **People** depending on your menu)

- **List offices:** **Branches** → **All Branches**.
- **Add office:** **Create Branch** → enter Office Name, Address, City, State, Zip, Country, Email, Phone, Mobile, Contact Person, etc. → Save.
- **Edit office:** From the list, **Edit** on a branch → change fields → Save.
- **View office:** Click the branch name (from list or from the Office column on the user list) to open the branch detail page.

---

## 4. View by office (reports / lists)

- **Ongoing Sheet (clients by office)**  
  **Clients** → **Ongoing Sheet** (or **Sheets** → **Ongoing**). Use the **Office** filter to see only clients assigned to selected office(s).

- **In Person (office visits)**  
  **In Person** → use the branch dropdown at the top to filter by office (All Branches / specific office). When **creating** an In Person check-in, you select the **Office** where the visit occurs.

- **User list**  
  The **Office** column on **Users** → **Active/Inactive** shows each user’s office; click the name to open that branch.

---

## 5. Quick reference

| Goal                         | Where to go                          | Action |
|-----------------------------|--------------------------------------|--------|
| Assign office to staff      | Users → Create/Edit User             | Select **Office** in Office DETAILS, save. |
| Assign/change client office | Clients Manager → client → Edit     | Select **Office** in the form, save. |
| View user’s office         | Users → list or User view            | Office column / Offices section. |
| View client’s office       | Clients Manager → client detail     | **Office** line (link to branch). |
| Add or edit offices         | Branches → Create/Edit Branch        | Fill office name, address, etc. |
| Filter clients by office    | Clients → Ongoing Sheet              | Use Office filter. |
| Filter check-ins by office  | In Person                            | Use branch dropdown. |
| Choose office for a visit   | In Person → Create In Person         | Select Office in the form. |

---

Validation: When you assign an office on user create/edit, the system checks that the selected value is a valid branch. If you leave office blank, it is stored as empty (nullable).
