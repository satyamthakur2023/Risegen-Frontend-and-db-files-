# 🚀 Risegen — Complete Setup Guide

## ✅ Requirements
| Tool | Download |
|------|---------|
| XAMPP 8.x | https://www.apachefriends.org |
| Python 3.8+ | https://www.python.org |

---

## 📁 Step 1 — Place Files
Copy the entire project folder into XAMPP htdocs:
```
C:\xampp\htdocs\risegen\
```

---

## 🗄️ Step 2 — Create Database
1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Open **phpMyAdmin** → http://localhost/phpmyadmin
3. Click **New** → name it `risegen` → **Create**
4. Click the `risegen` database → **Import** → choose `database_setup.sql` → **Go**

---

## ⚙️ Step 3 — Configure .env
Open `.env` in the project root and set:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=risegen
```

---

## 🌐 Step 4 — Open the App
```
http://localhost/risegen/index.php
```

---

## 🤖 Step 5 — Run Python MCQ Server
Double-click `run_mcq.bat` OR:
```bash
pip install flask flask-cors PyPDF2 nltk mysql-connector-python
python advanced_mcq_server_v2.py
```
Server runs at: **http://localhost:5002**

---

## 🔑 Default Admin Login
URL: `http://localhost/risegen/admin_login.php`

| Field | Value |
|-------|-------|
| Email | admin@risegen.com |
| Password | password |

> ⚠️ Change this password immediately after first login.

---

## 🗺️ Full File Map

### Public (No login required)
| File | Description |
|------|-------------|
| `index.php` | Landing page + Knowledge Rush quiz |
| `login.php` | User login with CSRF |
| `User_Registration.php` | User signup |
| `forgot_password.php` | Password reset |
| `contact.php` | Contact form |

### User Pages (Login required)
| File | Description |
|------|-------------|
| `welcome.php` | Main dashboard with charts |
| `dashboard.php` | Overview dashboard |
| `course.php` | Browse & enroll courses |
| `enrolled.php` | My enrolled courses + payment |
| `gamebox.php` | Timed entrance exam |
| `cert.php` | Certificate (≥70% score) |
| `blogs.php` | Blog listing |
| `profile.php` | View & update profile |
| `notification.php` | Last login security info |
| `credit.php` | Buy credits |
| `instructor.php` | Instructor profile |
| `leaderboard.php` | Global rankings |
| `search.php` | Search courses & blogs |
| `ai_assessment_fixed.php` | AI topic assessment |
| `Locked Features.php` | Premium features gate |
| `logout.php` | Secure logout |

### Admin Pages (Admin login required)
| File | Description |
|------|-------------|
| `admin_login.php` | Admin login |
| `admin.php` | Admin entry (redirects) |
| `admin_dashboard.php` | Stats + charts |
| `user_management.php` | View all users |
| `Profile_admin.php` | Edit any user profile |
| `admin_blog_manager.php` | Create/edit/delete blogs |
| `admin_course_manager.php` | Create/edit/delete courses |
| `admin_register.php` | Register new admin |
| `admin_logout.php` | Admin logout |

### Backend / API
| File | Description |
|------|-------------|
| `config.php` | Central DB config (reads .env) |
| `auth.php` | Session guard |
| `api.php` | MCQ questions + score saving |
| `realtime_api.php` | PDF/topic/answer REST API |
| `fetch_blogs.php` | Blog JSON API |
| `get_credits.php` | Credits JSON API |
| `save_jobs.php` | Save job endpoint |
| `user_details_update.php` | Profile update handler |
| `api/config.php` | API subfolder DB config |
| `api/login.php` | API login/register |
| `api/logout.php` | API logout |
| `api/courses.php` | Courses REST API |
| `api/check-session.php` | Session check endpoint |

### Python AI Server
| File | Description |
|------|-------------|
| `advanced_mcq_server_v2.py` | Flask server — PDF upload, TF-IDF, MCQ generation, MySQL persistence |
| `run_mcq.bat` | Windows launcher |

### Database
| File | Description |
|------|-------------|
| `database_setup.sql` | Master schema — ALL tables + seed data |

---

## 🔗 Connection Map
```
index.php → login.php / User_Registration.php
  └── welcome.php (dashboard)
        ├── course.php → enrolled.php
        ├── gamebox.php → api.php → test_results → cert.php
        ├── blogs.php → fetch_blogs.php
        ├── leaderboard.php → test_results
        ├── search.php → courses + blogs
        ├── ai_assessment_fixed.php → assessment_results
        ├── credit.php → users.credits
        ├── profile.php → user_details_update.php
        ├── notification.php → users.last_login
        └── logout.php

admin_login.php → admin_dashboard.php
  ├── user_management.php → Profile_admin.php
  ├── admin_blog_manager.php → blogs
  └── admin_course_manager.php → courses

Python (localhost:5002)
  ├── /api/upload → pdf_uploads + topics
  ├── /api/generate → questions
  ├── /api/session → test_sessions
  └── /api/answer → user_answers
```

---

## ⚠️ Common Issues
| Problem | Fix |
|---------|-----|
| Blank page | Enable `display_errors` in php.ini |
| DB connection failed | Check `.env` credentials |
| Certificate not showing | Score must be ≥70% |
| Python server won't start | Run `pip install flask flask-cors PyPDF2 nltk mysql-connector-python` |
| Admin login fails | Re-import `database_setup.sql` |

---

© 2026 Risegen — All rights reserved.
