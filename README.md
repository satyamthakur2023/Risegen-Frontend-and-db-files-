# 🎓 Risegen — Learn, Earn, Repeat Platform

> A full-stack ed-tech platform built with **PHP 8**, **Python (Flask)**, **MySQL**, and **Tailwind CSS**.
> Users learn skills, take AI-powered assessments, earn verified certificates, and access real job/gig listings.

[![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php)](https://php.net)
[![Python](https://img.shields.io/badge/Python-3.8%2B-3776AB?logo=python)](https://python.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql)](https://mysql.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?logo=tailwind-css)](https://tailwindcss.com)

---

## 🚀 Quick Start

```bash
# 1. Place files in XAMPP htdocs
# 2. Import database_setup.sql into MySQL
# 3. Edit .env with your DB credentials
# 4. Visit http://localhost/risegen/index.php
# 5. (Optional) Run Python MCQ server: double-click run_mcq.bat
```

> See **[SETUP.md](SETUP.md)** for the full step-by-step installation guide.

---

## 🔑 Default Admin Login

| Field | Value |
|-------|-------|
| URL | `/admin_login.php` |
| Email | `admin@risegen.com` |
| Password | `password` |

> ⚠️ Change this immediately after first login.

---

## 🌐 Public Pages (No login required)

| File | Description |
|------|-------------|
| `index.php` | Landing page — hero, Knowledge Rush quiz game, pricing, testimonials, live gig listings |
| `login.php` | User login — PDO, CSRF protection, session regeneration, IP tracking |
| `User_Registration.php` | User signup — password hashing, CSRF, validation |
| `forgot_password.php` | Password reset — generates secure token stored in DB |
| `contact.php` | Contact form — CSRF protected, saves to `contact_messages` table |
| `logout.php` | Destroys session, clears cookie, prevents back-button access |

---

## 📊 User Dashboard (Login required)

| File | Description |
|------|-------------|
| `welcome.php` | Main dashboard — 30-day study chart (Chart.js), enrolled courses, upcoming events |
| `dashboard.php` | Overview — live DB counts for courses, tests, jobs, blogs |
| `profile.php` | View & update username, email, password |
| `user_details_update.php` | Backend handler for profile updates |
| `notification.php` | Security panel — real last login time + IP from DB |
| `credit.php` | Credit balance + 3 purchase packs (Starter/Standard/Premium) |
| `get_credits.php` | JSON API — returns user credit balance |
| `enrolled.php` | My enrolled courses + UPI payment slip upload |
| `save_jobs.php` | Save job to user's saved list (session auth + PDO) |
| `leaderboard.php` | Global rankings from `test_results` table, highlights your rank |
| `search.php` | Search across courses and blogs from DB |
| `Locked Features.php` | Premium feature gate — real DB credit check, course progress |

---

## 🎮 Assessment & Gamification

| File | Description |
|------|-------------|
| `gamebox.php` | Entrance Exam Portal — timed MCQ, anti-cheat tab detection, score calculation |
| `cert.php` | Certificate Generator — printable PDF, gated at ≥70% score |
| `api.php` | MCQ questions API — prepared statements, anti-repeat logic, score + cert saving |
| `game.html` | Standalone HTML quiz game |

---

## 🤖 AI-Powered MCQ Generator (Python/Flask)

| File | Description |
|------|-------------|
| `advanced_mcq_server_v2.py` | Flask server — PDF upload, TF-IDF summarization, topic detection, 4 MCQ types, **MySQL persistence** |
| `db_connection.py` | Legacy in-memory DB class (superseded by MySQL) |
| `advanced-mcq-generator.html` | MCQ generator frontend UI |
| `advanced-mcq-generator-v2.html` | V2 frontend with improved UX |
| `fixed_mcq_generator.html` | Stable MCQ generator UI |
| `ai_assessment_fixed.php` | PHP AI assessment — topic-based question generation, saves results to DB |
| `ai_assessment_rag_frontend.html` | RAG-based AI assessment frontend |
| `run_mcq.bat` | Windows launcher — installs all pip packages + starts Flask server |

---

## 🔧 Backend & API

| File | Description |
|------|-------------|
| `config.php` | Central DB config — reads from `.env`, dual MySQLi + PDO |
| `auth.php` | Session guard — redirects unauthenticated users |
| `realtime_api.php` | REST API — session auth, PDF uploads, answer saving, live score |
| `realtime_client.js` | JS client for real-time API |
| `fetch_blogs.php` | Blog JSON API — search, category filter, pagination |
| `blogs.php` | Blog listing — infinite scroll, category pills, reading mode |
| `course.php` | Course catalog — filter, sort, modal, enroll button |
| `instructor.php` | Instructor profile page |
| `master_injector.php` | Blog seeder — injects 110 sample blog posts |

---

## 👑 Admin Panel (Admin login required)

| File | Description |
|------|-------------|
| `admin_login.php` | Admin login — PDO, password_verify, session |
| `admin_logout.php` | Admin session destroy |
| `admin.php` | Entry point — redirects to login or dashboard |
| `admin_dashboard.php` | Stats — total users, 7-day active, Chart.js engagement trend, recent signups |
| `admin_register.php` | Register new admin accounts |
| `admin_creation.php` | Admin creation handler |
| `user_management.php` | View all users with nav to blog/course managers |
| `Profile_admin.php` | Edit any user — dynamic `?id=`, real session auth, redirects to user_management |
| `admin_blog_manager.php` | Full CRUD for blogs — create, edit, delete |
| `admin_course_manager.php` | Full CRUD for courses — create, edit, delete |

---

## 🗄️ Database

| File | Description |
|------|-------------|
| `database_setup.sql` | **Master schema** — 16 tables + seed data (admin, courses, MCQ questions) |
| `api/database.sql` | Legacy partial schema (superseded by master) |
| `api/config.php` | API subfolder DB config — reads from `.env` |
| `api/check-session.php` | Session validation endpoint |
| `api/courses.php` | Courses REST API — GET all, POST create |
| `api/login.php` | API login + register endpoint |
| `api/logout.php` | API logout endpoint |

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, Tailwind CSS, Font Awesome, Lucide Icons, Vanilla JS |
| Backend | PHP 8+ (PDO + MySQLi), Sessions, CSRF Tokens |
| AI/ML Server | Python 3, Flask, NLTK, PyPDF2, TF-IDF, mysql-connector-python |
| Database | MySQL 8 — 16 tables, seeded data |
| Charts | Chart.js |
| Security | CSRF tokens, session regeneration, prepared statements, `.env` credentials |
| Fonts | Plus Jakarta Sans, Space Grotesk |

---

## 🔗 How Everything Connects

```
Browser
  │
  ├── index.php ──────────────────────────────► Landing page
  │     ├── login.php ──────────────────────────► welcome.php (dashboard)
  │     ├── User_Registration.php ──────────────► login.php
  │     ├── forgot_password.php ────────────────► users.reset_token
  │     └── contact.php ────────────────────────► contact_messages table
  │
  ├── welcome.php / dashboard.php
  │     ├── course.php ─────────────────────────► enrolled.php → course_enrollments
  │     ├── gamebox.php ────────────────────────► api.php → test_results → cert.php
  │     ├── blogs.php ──────────────────────────► fetch_blogs.php → blogs table
  │     ├── leaderboard.php ─────────────────────► test_results table
  │     ├── search.php ──────────────────────────► courses + blogs tables
  │     ├── ai_assessment_fixed.php ─────────────► assessment_results table
  │     ├── credit.php ──────────────────────────► users.credits
  │     ├── profile.php ─────────────────────────► user_details_update.php → users
  │     ├── notification.php ─────────────────────► users.last_login_time
  │     └── logout.php
  │
  ├── admin_login.php ─────────────────────────► admin_dashboard.php
  │     ├── user_management.php ────────────────► Profile_admin.php → users
  │     ├── admin_blog_manager.php ─────────────► blogs table
  │     └── admin_course_manager.php ───────────► courses table
  │
  └── Python Server (localhost:5002)
        ├── /api/upload ────────────────────────► pdf_uploads + topics tables
        ├── /api/generate ──────────────────────► questions table
        ├── /api/session ───────────────────────► test_sessions table
        └── /api/answer ────────────────────────► user_answers table
```

---

## 🔑 Key Features

- ✅ User registration, login, CSRF protection, session regeneration
- ✅ Timed entrance exam with anti-cheat tab detection
- ✅ Auto-generated printable certificates (≥70% pass threshold)
- ✅ AI MCQ generator from PDF uploads — TF-IDF + NLTK + MySQL persistence
- ✅ AI topic-based assessment with DB result saving
- ✅ Admin dashboard with live Chart.js engagement charts
- ✅ Full CRUD for blogs and courses from admin panel
- ✅ Credit/payment system with 3 purchase tiers
- ✅ Global leaderboard from real test results
- ✅ Global search across courses and blogs
- ✅ Job listings via live Render API
- ✅ Course enrollment with UPI payment slip upload
- ✅ Password reset with secure token
- ✅ Contact form with DB storage
- ✅ Premium feature gating based on credits
- ✅ Credentials secured via `.env` file (not hardcoded)
- ✅ All SQL queries use prepared statements (no injection)

---

## ⚙️ How to Run

### PHP App (XAMPP)
1. Copy files to `C:\xampp\htdocs\risegen\`
2. Import `database_setup.sql` into phpMyAdmin
3. Edit `.env`:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=risegen
```
4. Visit `http://localhost/risegen/index.php`

### Python MCQ Server
```bash
pip install flask flask-cors PyPDF2 nltk mysql-connector-python
python advanced_mcq_server_v2.py
# Runs on http://localhost:5002
```
Or double-click `run_mcq.bat` on Windows.

---

## 📁 Project Structure

```
risegen/
├── index.php              → Landing page
├── login.php              → User auth
├── User_Registration.php  → Signup
├── welcome.php            → Main dashboard
├── dashboard.php          → Overview dashboard
├── gamebox.php            → Exam portal
├── cert.php               → Certificate
├── blogs.php              → Blog listing
├── course.php             → Course catalog
├── enrolled.php           → My courses
├── leaderboard.php        → Rankings
├── search.php             → Global search
├── credit.php             → Buy credits
├── profile.php            → User profile
├── notification.php       → Security info
├── contact.php            → Contact form
├── forgot_password.php    → Password reset
├── ai_assessment_fixed.php→ AI assessment
├── Locked Features.php    → Premium gate
├── admin_login.php        → Admin auth
├── admin_dashboard.php    → Admin stats
├── admin_blog_manager.php → Blog CRUD
├── admin_course_manager.php→ Course CRUD
├── user_management.php    → User list
├── Profile_admin.php      → Edit user
├── config.php             → DB config
├── auth.php               → Session guard
├── api.php                → MCQ API
├── realtime_api.php       → REST API
├── fetch_blogs.php        → Blog API
├── advanced_mcq_server_v2.py → Flask AI server
├── database_setup.sql     → Full DB schema
├── .env                   → DB credentials (not committed)
├── .gitignore             → Ignores .env
├── SETUP.md               → Full setup guide
└── api/                   → REST API subfolder
    ├── config.php
    ├── login.php
    ├── logout.php
    ├── courses.php
    └── check-session.php
```

---

## 🌐 Live Hosting

- PHP App: [ByetHost](https://byethost7.com)
- Jobs API: [https://risegen.onrender.com](https://risegen.onrender.com)
- GitHub: [https://github.com/satyamthakur2023/Risegen-Frontend-and-db-files-](https://github.com/satyamthakur2023/Risegen-Frontend-and-db-files-)

---

## ⚠️ Common Issues

| Problem | Fix |
|---------|-----|
| Blank page | Enable `display_errors` in `php.ini` |
| DB connection failed | Check `.env` credentials match phpMyAdmin |
| Certificate not showing | Score must be ≥70% in URL |
| Python server won't start | Run `pip install flask flask-cors PyPDF2 nltk mysql-connector-python` |
| Admin login fails | Re-import `database_setup.sql` |
| Credits not updating | Check `users` table has `credits` column |

---

© 2026 Risegen — All rights reserved.
