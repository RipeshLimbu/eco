-- Create database if not exists
CREATE DATABASE IF NOT EXISTS waste_management;
USE waste_management;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'collector', 'user') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create complaints table
CREATE TABLE IF NOT EXISTS complaints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'rejected', 'assigned') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create assignments table
CREATE TABLE IF NOT EXISTS assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    complaint_id INT NOT NULL,
    collector_id INT NOT NULL,
    assigned_by INT NOT NULL,
    notes TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id),
    FOREIGN KEY (collector_id) REFERENCES users(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);

-- Insert default admin user
INSERT INTO users (username, password, email, role, full_name, phone) 
VALUES (
    'admin',
    '$2a$10$P/hEhW01CHoHjt0gPkOdnuKsbG3MgJf/imFBCrgnFHAh1uLdc2ze.',  -- admin@123
    'admin@waste.com',
    'admin',
    'System Admin',
    '1234567890'
);

-- Insert default collector
INSERT INTO users (username, password, email, role, full_name, phone) 
VALUES (
    'nishan',
    '$2a$10$DPXkN9wZ8a2hPBZMkTStO.aTVFE10LJabc.vhXJZsGFS2vS7ngYbu',  -- nishan@123
    'nishan@waste.com',
    'collector',
    'Nishan Pradhan',
    '9876543210'
);

-- Insert default user
INSERT INTO users (username, password, email, role, full_name, phone) 
VALUES (
    'ripesh',
    '$2a$10$Y2zwRSP0Z3bg.E2EwRRZIOdx9f7ChYVDgTvdKJt9cjB5uWS3kMzru',  -- ripesh@123
    'ripesh@waste.com',
    'user',
    'Ripesh Limbu',
    '9807766556788'
);