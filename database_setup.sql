-- Database Schema for PDF MCQ Generator
CREATE DATABASE IF NOT EXISTS risegen;
USE risegen;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PDF uploads table
CREATE TABLE pdf_uploads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Topics table
CREATE TABLE topics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pdf_id INT,
    topic_name VARCHAR(100) NOT NULL,
    relevance_score DECIMAL(3,2),
    FOREIGN KEY (pdf_id) REFERENCES pdf_uploads(id)
);

-- Questions table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pdf_id INT,
    topic_id INT,
    question_text TEXT NOT NULL,
    correct_answer VARCHAR(500) NOT NULL,
    option_a VARCHAR(500),
    option_b VARCHAR(500),
    option_c VARCHAR(500),
    option_d VARCHAR(500),
    difficulty ENUM('Easy', 'Medium', 'Hard') DEFAULT 'Medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pdf_id) REFERENCES pdf_uploads(id),
    FOREIGN KEY (topic_id) REFERENCES topics(id)
);

-- Test sessions table
CREATE TABLE test_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    pdf_id INT,
    total_questions INT,
    score INT DEFAULT 0,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (pdf_id) REFERENCES pdf_uploads(id)
);

-- User answers table
CREATE TABLE user_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id INT,
    question_id INT,
    user_answer VARCHAR(500),
    is_correct BOOLEAN DEFAULT FALSE,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES test_sessions(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);