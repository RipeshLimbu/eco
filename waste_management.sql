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
-- Create contact_submissions table to store form submissions
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--payment table
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,          -- Auto-incremented ID for each record
    transaction_id VARCHAR(255) NOT NULL,       -- Unique transaction ID for each payment
    amount DECIMAL(10, 2) NOT NULL,            -- Amount paid
    total_amount DECIMAL(10, 2) NOT NULL,      -- Total amount of the transaction
    mobile VARCHAR(15) NOT NULL,               -- Mobile number associated with the payment
    status ENUM('paid', 'due') NOT NULL,       -- Payment status (either 'paid' or 'due')
    purchase_order_id VARCHAR(255) NOT NULL,   -- Unique ID for the purchase order
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp of when the payment record is created
    UPDATE payments SET status = 'due' WHERE status IS NULL OR status = '';
    ALTER TABLE payments MODIFY status VARCHAR(20) NOT NULL DEFAULT 'due';
-- Step 1: Add the necessary columns to the payments table
ALTER TABLE payments
ADD COLUMN admin_id INT,
ADD COLUMN user_id INT,
ADD COLUMN collector_id INT;

-- Step 2: Add the foreign key constraints
ALTER TABLE payments
ADD CONSTRAINT fk_admin_id FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_collector_id FOREIGN KEY (collector_id) REFERENCES users(id) ON DELETE CASCADE;

-- Step 3: Update payments status where needed
UPDATE payments SET status = 'due' WHERE status IS NULL OR status = '';

-- Step 4: Alter payments table to change status column to a VARCHAR with default value 'due'
ALTER TABLE payments MODIFY status VARCHAR(20) NOT NULL DEFAULT 'due';

);
