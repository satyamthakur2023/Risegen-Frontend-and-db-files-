-- ============================================================
-- RISEGEN PLATFORM - COMPLETE DATABASE SCHEMA v2.0
-- Import this ONCE in phpMyAdmin after creating your database
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS users (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    username            VARCHAR(50)  UNIQUE NOT NULL,
    email               VARCHAR(100) UNIQUE NOT NULL,
    password_hash       VARCHAR(255) NOT NULL,
    role                ENUM('student','instructor','admin') DEFAULT 'student',
    status              ENUM('active','inactive','banned') DEFAULT 'active',
    bio                 TEXT,
    credits             INT DEFAULT 0,
    last_login_ip       VARCHAR(45),
    last_login_time     DATETIME,
    last_activity       DATETIME,
    reset_token         VARCHAR(64),
    reset_token_expires DATETIME,
    updated_at          DATETIME,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    admin_id      INT PRIMARY KEY AUTO_INCREMENT,
    username      VARCHAR(50)  UNIQUE NOT NULL,
    email         VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    last_login    DATETIME,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_logins (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id    INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS courses (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    title      VARCHAR(255) NOT NULL,
    `desc`     TEXT,
    level      ENUM('Beginner','Intermediate','Advanced') DEFAULT 'Beginner',
    rating     VARCHAR(10) DEFAULT '5.0',
    time       VARCHAR(50),
    cat        VARCHAR(50),
    instructor VARCHAR(100),
    img        VARCHAR(500),
    price      INT DEFAULT 0,
    enrolled   INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS enrollments (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT NOT NULL,
    course_id   INT NOT NULL,
    progress    INT DEFAULT 0,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS course_enrollments (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    user_id        INT NOT NULL,
    course_id      INT NOT NULL,
    payment_status ENUM('pending','confirmed','rejected') DEFAULT 'pending',
    payment_slip   VARCHAR(500),
    payment_time   DATETIME,
    access_granted TINYINT(1) DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blogs (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    title      VARCHAR(255) NOT NULL,
    category   VARCHAR(100),
    content    LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS questions (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    pdf_id         INT,
    topic_id       INT,
    question_text  TEXT NOT NULL,
    options        JSON,
    correct_answer VARCHAR(500) NOT NULL,
    question_type  VARCHAR(50) DEFAULT 'mcq',
    difficulty     ENUM('easy','medium','hard') DEFAULT 'medium',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS test_results (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    username   VARCHAR(50) NOT NULL,
    score      INT NOT NULL,
    status     ENUM('Passed','Failed') NOT NULL,
    cert_id    VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_exams (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    username    VARCHAR(50) NOT NULL,
    question_id INT NOT NULL,
    taken_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pdf_uploads (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id     INT,
    filename    VARCHAR(255) NOT NULL,
    file_path   VARCHAR(500) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS topics (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    pdf_id          INT,
    name            VARCHAR(100) NOT NULL,
    relevance_score DECIMAL(5,2),
    FOREIGN KEY (pdf_id) REFERENCES pdf_uploads(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS test_sessions (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT,
    pdf_id          INT,
    total_questions INT,
    score           INT DEFAULT 0,
    start_time      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time        TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS user_answers (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    session_id  INT,
    question_id INT,
    user_answer VARCHAR(500),
    is_correct  TINYINT(1) DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS saved_jobs (
    id       INT PRIMARY KEY AUTO_INCREMENT,
    user_id  INT NOT NULL,
    job_id   INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    subject    VARCHAR(255) NOT NULL,
    message    TEXT NOT NULL,
    is_read    TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_daily_summary (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    user_id             INT NOT NULL,
    summary_date        DATE NOT NULL,
    total_study_minutes INT DEFAULT 0,
    quizzes_taken       INT DEFAULT 0,
    UNIQUE KEY unique_user_date (user_id, summary_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS assessment_results (
    id              INT PRIMARY KEY AUTO_INCREMENT,
    user_id         INT,
    subject         VARCHAR(100),
    total_questions INT,
    correct_answers INT,
    percentage      DECIMAL(5,2),
    passed          TINYINT(1) DEFAULT 0,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SET FOREIGN_KEY_CHECKS = 1;

-- Default admin (password: admin123)
INSERT IGNORE INTO admins (username, email, password_hash)
VALUES ('admin', 'admin@risegen.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample courses
INSERT IGNORE INTO courses (id, title, `desc`, level, rating, time, cat, instructor, img, price, enrolled) VALUES
(1,'Full Stack Web Development','Master HTML, CSS, JS, React, Node.js.','Intermediate','4.8','8h 30m','development','John Parker','https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&q=80',299,1250),
(2,'Machine Learning Basics','Train models and deploy ML apps with Python.','Advanced','4.9','10h 0m','ai','Dr. Aisha Khan','https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=800&q=80',399,890),
(3,'UI/UX Design for Beginners','Learn Figma, typography, wireframing.','Beginner','4.6','6h 45m','design','Elena Rose','https://images.unsplash.com/photo-1590608897129-79c9d9f7b6f1?w=800&q=80',199,2100),
(4,'Entrepreneurship Essentials','Start and scale your business.','Intermediate','4.7','5h 20m','business','Michael Stone','https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=800&q=80',249,567),
(5,'Data Science with Python','Data visualization and statistical analysis.','Advanced','4.9','12h 15m','ai','Dr. Lin Wei','https://images.unsplash.com/photo-1581091012184-9d6c6a2333d3?w=800&q=80',449,1890),
(6,'Digital Marketing Strategy','Master SEO, SEM, and social media.','Beginner','4.5','7h 0m','business','Sarah Lee','https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80',179,3200);

-- Sample MCQ questions
INSERT IGNORE INTO questions (question_text, options, correct_answer, question_type, difficulty) VALUES
('What does HTML stand for?','["HyperText Markup Language","HighText Machine Language","HyperText and links Markup Language","None of these"]','HyperText Markup Language','mcq','easy'),
('Which language is used for styling web pages?','["HTML","JQuery","CSS","XML"]','CSS','mcq','easy'),
('What does CSS stand for?','["Colorful Style Sheets","Computer Style Sheets","Cascading Style Sheets","Creative Style Sheets"]','Cascading Style Sheets','mcq','easy'),
('Which HTML tag defines an internal style sheet?','["<css>","<script>","<style>","<link>"]','<style>','mcq','medium'),
('What is the correct JS syntax to change HTML content?','["document.getElement(\"p\").innerHTML=\"New\"","document.getElementById(\"p\").innerHTML=\"New\"","document.getElementByName(\"p\").innerHTML=\"New\"","#p.innerHTML=\"New\""]','document.getElementById("p").innerHTML="New"','mcq','medium'),
('Which company developed PHP?','["Rasmus Lerdorf","Microsoft","Sun Microsystems","Oracle"]','Rasmus Lerdorf','mcq','medium'),
('What does SQL stand for?','["Structured Query Language","Strong Question Language","Structured Question Language","Simple Query Language"]','Structured Query Language','mcq','easy'),
('Which is NOT a JavaScript framework?','["React","Angular","Laravel","Vue"]','Laravel','mcq','medium'),
('What is the default port for MySQL?','["3306","8080","5432","1521"]','3306','mcq','hard'),
('Which HTTP method sends data to a server?','["GET","POST","PUT","DELETE"]','POST','mcq','easy');
