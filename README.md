# LevelX Event Management & Registration System

A lightweight, modern, responsive PHP & MySQL web application where users can discover upcoming events, register for an event with automatic pre-selection, and view their registration summary. Administrators can securely log in to review all registrations, view attendee details, and manage records.

Built strictly according to the specifications in [PHP Task.pdf](PHP%20Task.pdf).

---

## 🛠️ Tech Stack (Mandatory Requirements)
- **Backend:** Native PHP 8.x (Modular architecture, PDO with prepared statements)
- **Frontend:** HTML5, CSS3 (Modern responsive card design, custom UI), JavaScript (Vanilla ES6)
- **Database:** MySQL / MariaDB (via XAMPP)

---

## 🚀 Key Features

### 1. Public Portal
- **Homepage (`index.php`)**:
  - Displays a clean, responsive grid of upcoming events with date/time badges, location, and description.
  - Each event has a prominent **"Register For Event"** button linking directly to the registration form.
- **Registration Form (`register.php`)**:
  - Form Fields: **Name**, **Email**, and **Event (auto-filled)**.
  - Auto-selects the event when clicked from the homepage (`?event_id=X`).
  - **Client-Side Validation (`assets/js/main.js`)**: Real-time validation for non-empty fields and valid email syntax.
  - **Server-Side Validation**: Sanitization, `filter_var()` email checks, event existence verification, and CSRF token security.
  - Saves the record into MySQL with prepared statements and redirects to the success page.
- **Success Page (`success.php`)**:
  - Displays a confirmation ticket/summary card with:
    - User's Name & Email
    - Event Title, Date, Time & Venue
    - Registration Reference ID & Timestamp
    - Action buttons: "Print Ticket / Summary" and "Browse More Events".

### 2. Admin Portal
- **Admin Authentication (`admin/login.php`, `admin/logout.php`, `includes/auth.php`)**:
  - Secure session management with `password_verify()` and `session_regenerate_id()`.
  - Auth guards on all admin routes redirecting unauthenticated users to login.
  - Default Credentials:
    - **Username:** `admin`
    - **Password:** `admin123`
- **Registrations Table (`admin/index.php`)**:
  - Required table columns: **ID**, **Name**, **Email**, **Event**, **Date Registered**, and **Action (View, delete)**.
  - KPI stat cards: Total Registrations count, Active Events count.
  - Real-time instant search/filter by name, email, or event title.
- **View Action (`admin/view.php`)**:
  - Interactive popup modal + dedicated details page with full registration breakdown.
- **Delete Action (`admin/delete.php`)**:
  - Secure POST-only deletion protected with confirmation modal and CSRF tokens.

---

## 📂 Project Directory Structure

```
Event Management/
├── assets/
│   ├── css/
│   │   ├── style.css           # Modern public CSS design system
│   │   └── admin.css           # Admin dashboard, tables, modals & badges
│   └── js/
│       ├── main.js             # Client-side validation & UI interactions
│       └── admin.js            # Live search filter, view & delete modals
├── config/
│   └── db.php                  # PDO connection with error handling & charset
├── database/
│   └── schema.sql              # Database schema with events & admin seeds
├── includes/
│   ├── auth.php                # Admin authentication & CSRF helper
│   ├── header.php              # Shared public navigation header
│   └── footer.php              # Shared public footer & scripts
├── admin/
│   ├── index.php               # Admin registrations table (ID, Name, Email, Event, Date, Actions)
│   ├── login.php               # Admin login page
│   ├── logout.php              # Admin logout script
│   ├── view.php                # Dedicated registration details view
│   └── delete.php              # Secure deletion handler
├── index.php                   # Homepage listing upcoming events
├── register.php                # Registration form with auto-filled event
├── success.php                 # Registration confirmation & ticket card
├── setup.php                   # 1-Click automatic database installer
├── README.md                   # Documentation & Setup Guide
└── PHP Task.pdf                # Requirements specification document
```

---

## ⚙️ Quick Start & Installation Guide

### Step 1: Start XAMPP Services
Open the **XAMPP Control Panel** and click **Start** for both **Apache** and **MySQL**.

### Step 2: Initialize Database (1-Click)
Navigate to the setup wizard in your browser:
```
http://localhost/LEVELX%20MICRO/Event%20Management/setup.php
```
*Alternatively, run from CLI:*
```bash
php setup.php
```
This automatically creates the `event_management` database, sets up the tables (`events`, `registrations`, `admins`), seeds upcoming events, and configures the default admin user.

### Step 3: Browse the Application
- **Events Homepage:** `http://localhost/LEVELX%20MICRO/Event%20Management/index.php`
- **Register for Event:** Click "Register" on any event card or go to `register.php`
- **Admin Portal:** `http://localhost/LEVELX%20MICRO/Event%20Management/admin/login.php`
  - **Username:** `admin`
  - **Password:** `admin123`
