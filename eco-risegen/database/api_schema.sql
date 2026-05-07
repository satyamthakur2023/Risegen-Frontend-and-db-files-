-- Run this in phpMyAdmin on your byethost7 database

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'instructor') DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  `desc` TEXT,
  level VARCHAR(50),
  rating VARCHAR(10) DEFAULT '5.0',
  time VARCHAR(50),
  cat VARCHAR(50),
  instructor VARCHAR(100),
  img VARCHAR(500),
  price INT DEFAULT 0,
  enrolled INT DEFAULT 0,
  progress INT DEFAULT 0,
  createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
