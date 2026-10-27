-- Online Course Management System
-- Import this file using phpMyAdmin or the MySQL command line.
CREATE DATABASE IF NOT EXISTS online_course_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE online_course_db;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE courses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  category VARCHAR(80) NOT NULL,
  description TEXT NOT NULL,
  duration_hours INT UNSIGNED NOT NULL DEFAULT 1,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  instructor_id INT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_course_instructor FOREIGN KEY (instructor_id)
    REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE enrollments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  enrolled_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_student_course (student_id, course_id),
  CONSTRAINT fk_enrollment_student FOREIGN KEY (student_id)
    REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_enrollment_course FOREIGN KEY (course_id)
    REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Demo admin account. Password: Admin@123
-- This hash is generated for the password above using PHP password_hash().
INSERT INTO users (name,email,password_hash,role) VALUES
('CourseHub Administrator','admin@coursehub.test',
'$2y$12$pILI6D4sY9j6BGaDO4jk4O/x5AA/VSydtpOVl85J9LgHbJk7Hu9da','admin');

INSERT INTO courses (title,category,description,duration_hours,price,instructor_id) VALUES
('Introduction to Web Development','Technology','Learn the basics of HTML, CSS and PHP by building simple web pages.',12,0.00,1),
('Business Communication','Business','Improve professional writing, presentations and workplace communication.',8,499.00,1),
('Database Fundamentals','Technology','Understand tables, keys, relationships and essential SQL queries.',10,299.00,1);
