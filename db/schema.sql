-- SafeSphere Database Schema
-- Import this first: mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS safesphere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE safesphere;

-- ============ USERS ============
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    cyber_score INT DEFAULT 40,
    streak_count INT DEFAULT 0,
    last_active_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============ MODULE ATTEMPTS ============
-- One row per scenario answered, across all 4 modules.
CREATE TABLE IF NOT EXISTS attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    module_key VARCHAR(30) NOT NULL,        -- 'phishing' | 'upi' | 'socialeng' | 'network'
    scenario_id VARCHAR(30) NOT NULL,
    user_choice VARCHAR(30) NOT NULL,
    is_correct TINYINT(1) NOT NULL,
    points_awarded INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_module (user_id, module_key)
) ENGINE=InnoDB;

-- ============ NETWORK AUDIT CHECKLIST RESULTS ============
CREATE TABLE IF NOT EXISTS audit_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    check_id VARCHAR(30) NOT NULL,
    status ENUM('pending','pass','fail') DEFAULT 'pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_check (user_id, check_id)
) ENGINE=InnoDB;

-- ============ CERTIFICATES ISSUED ============
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cert_key VARCHAR(50) NOT NULL,          -- e.g. 'phishing_specialist'
    title VARCHAR(150) NOT NULL,
    credential_id VARCHAR(30) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============ FORENSIC TOOL SUBMISSIONS (log of analyses run) ============
CREATE TABLE IF NOT EXISTS forensic_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tool_type VARCHAR(30) NOT NULL,          -- 'header' | 'url' | 'apk_permission'
    input_sample VARCHAR(255) NOT NULL,
    verdict VARCHAR(30) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

