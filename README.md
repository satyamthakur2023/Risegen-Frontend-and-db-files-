# 🎓 Risegen — Learn, Earn, Repeat Platform

> A full-stack ed-tech platform built with PHP, Python (Flask), MySQL, and Tailwind CSS.  
> Risegen lets users learn skills, take AI-powered assessments, earn certificates, and access real job/gig listings.

---

## 🚀 What We Built

### 🌐 Public Frontend
| File | What it does |
|------|-------------|
| `index.php` | Landing page — hero section, Knowledge Rush quiz game, pricing, testimonials, live gig listings |
| `welcome.php` / `welcome-1.php` | Post-login welcome screens |
| `login.php` | User login with PDO, session management, IP tracking |
| `User_Registration.php` | User signup with password hashing, validation, and redirect |
| `logout.php` | Destroys session and redirects to login |

---

### 📊 User Dashboard
| File | What it does |
|------|-------------|
| `dashboard.php` | Main user dashboard — shows courses, tests, jobs, blogs overview cards |
| `profile.php` | View user profile |
| `Profile_update.php` | Update profile details |
| `user_details_update.php` | Backend handler for profile field updates |
| `notification.php` | User notifications panel |
| `credit.php` | Shows user credit balance |
| `get_credits.php` | API endpoint to fetch credits |
| `enrolled.php` | Shows courses the user is enrolled in |
| `save_jobs.php` | Saves job listings to user's saved list |

---

### 🎮 Assessment & Gamification
| File | What it does |
|------|-------------|
| `gamebox.php` | **Entrance Exam Portal** — timed MCQ test fetched from DB, anti-cheat tab detection, score calculation |
| `cert.php` | **Certificate Generator** — auto-generates a printable PDF certificate for users who score ≥70% |
| `game.html` | Standalone HTML quiz game |
| `api.php` | Serves MCQ questions from DB and saves scores/cert IDs |

---

### 🤖 AI-Powered MCQ Generator (Python/Flask)
| File | What it does |
|------|-------------|
| `advanced_mcq_server_v2.py` | **Flask API server** — uploads PDFs, extracts text, runs TF-IDF summarization, detects topics, generates 4 types of MCQs (fill-blank, true/false, definition, comprehension) |
| `db_connection.py` | In-memory database class for MCQ sessions, answers, and scoring |
| `New.py` | Additional Python utility script |
| `advanced-mcq-generator.html` | Frontend UI for the MCQ generator |
| `advanced-mcq-generator-v2.html` | V2 frontend with improved UX |
| `fixed_mcq_generator.html` | Stable/fixed version of MCQ generator UI |
| `ai_assessment_fixed.php` | PHP wrapper for AI assessment integration |
| `ai_assessment_rag_frontend.html` | RAG-based AI assessment frontend |
| `run_app.bat` | Windows batch script to start the Flask server |
| `run_mcq.bat` | Windows batch script to run the MCQ server |

---

### 🔧 Backend & API
| File | What it does |
|------|-------------|
| `config.php` | **Central DB config** — dual MySQLi + PDO connection, constants, error handling |
| `auth.php` | Session guard — redirects unauthenticated users to login |
| `master_injector.php` | Shared PHP includes injector |
| `realtime_api.php` | REST API — handles PDF uploads, answer saving, topic/question fetching, live score |
| `realtime_client.js` | JS client for real-time API communication |
| `fetch_blogs.php` | Fetches blog posts from DB |
| `blogs.php` | Blog listing page |
| `course.php` | Course detail page |
| `instructor.php` | Instructor profile/listing page |

---

### 👑 Admin Panel
| File | What it does |
|------|-------------|
| `admin_login.php` | Admin-only login page |
| `admin_logout.php` | Admin session destroy |
| `admin_dashboard.php` | **Advanced admin dashboard** — total users, 7-day active users, engagement trend chart (Chart.js), recent signups |
| `admin_register.php` | Register new admin accounts |
| `admin_creation.php` | Admin account creation handler |
| `admin.php` | Admin panel entry point |
| `Profile_admin.php` | Admin profile management |
| `user_management.php` | View and manage all users |
| `Locked Features.php` | Premium/locked feature gate |

---

### 🗄️ Database
| File | What it does |
|------|-------------|
| `database_setup.sql` | Main schema — users, courses, enrollments, certificates, credits, jobs |
| `api/database.sql` | API-specific schema — pdf_uploads, topics, questions, sessions, answers |
| `api/config.php` | API folder DB config |
| `api/check-session.php` | Session validation endpoint |
| `api/courses.php` | Courses API endpoint |
| `api/login.php` | API login handler |
| `api/logout.php` | API logout handler |

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, Tailwind CSS, Font Awesome, Vanilla JS |
| Backend | PHP 8+ (PDO + MySQLi), Sessions |
| AI/ML Server | Python 3, Flask, NLTK, PyPDF2, TF-IDF |
| Database | MySQL (hosted on ByetHost) |
| Charts | Chart.js |
| Fonts | Plus Jakarta Sans, Space Grotesk |

---

## ⚙️ How to Run

### PHP App (XAMPP / ByetHost)
1. Place all files in your `htdocs` or hosting root
2. Import `database_setup.sql` into your MySQL database
3. Update credentials in `config.php`:
```php
$db_host = 'your_host';
$db_user = 'your_user';
$db_pass = 'your_password';
$db_name = 'your_db_name';
```
4. Visit `index.php` in your browser

### Python MCQ Server
```bash
pip install flask flask-cors PyPDF2 nltk
python advanced_mcq_server_v2.py
# Server runs on http://localhost:5002
```
Or just double-click `run_mcq.bat` on Windows.

---

## 🔑 Key Features

- ✅ User registration, login, session management
- ✅ Timed entrance exam with anti-cheat tab detection
- ✅ Auto-generated printable certificates (≥70% pass threshold)
- ✅ AI MCQ generator from PDF uploads using TF-IDF + NLTK
- ✅ Admin dashboard with live engagement charts
- ✅ Credit/payment system
- ✅ Job listings and gig access
- ✅ Blog system
- ✅ Course enrollment tracking
- ✅ Real-time scoring API

---

## 📁 Project Structure

```
risegen/
├── public/          → Landing, login, register, dashboard
├── backend/
│   ├── auth/        → auth.php, master_injector.php
│   ├── admin/       → admin panel files
│   ├── user/        → user management files
│   └── api/         → REST API endpoints
├── database/        → SQL schema files
├── python/          → Flask MCQ server
└── assets/          → Images and static files
```

---

## 🌐 Live Hosting

- PHP App: [ByetHost](https://byethost7.com)
- Jobs API: [https://risegen.onrender.com](https://risegen.onrender.com)

---

© 2026 Risegen — All rights reserved.
