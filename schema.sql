-- ==========================================
-- JIRALITE DATABASE SCHEMA
-- ==========================================
-- Production-Ready MySQL/MariaDB Schema
-- Last Updated: January 9, 2026
-- ==========================================

-- Set strict SQL mode
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

-- ==========================================
-- USERS TABLE
-- ==========================================
-- Stores all application users
-- Roles: admin, developer, qa
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique user identifier',
    name VARCHAR(100) NOT NULL COMMENT 'User full name',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Unique email address for login',
    password VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed password',
    remember_token VARCHAR(255) NULL COMMENT 'Token for Remember Me functionality (30-day expiry)',
    role ENUM('admin', 'developer', 'qa') NOT NULL DEFAULT 'developer' COMMENT 'User role - determines permissions',
    remember_token VARCHAR(255) NULL COMMENT 'Token for Remember Me functionality (30-day expiry)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    
    -- Indexes for common queries
    INDEX idx_email (email) COMMENT 'Fast lookup by email for login',
    INDEX idx_role (role) COMMENT 'Fast queries by user role',
    INDEX idx_remember_token (remember_token) COMMENT 'Fast lookup by remember token',
    
    -- Constraints
    CONSTRAINT chk_role CHECK (role IN ('admin', 'developer', 'qa')),
    CONSTRAINT chk_email_format CHECK (email LIKE '%@%.%')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User accounts and authentication';

-- ==========================================
-- BUGS TABLE
-- ==========================================
-- Stores all bug reports and tasks
-- Statuses: todo, in_progress, done
-- Priorities: low, medium, high, critical
CREATE TABLE IF NOT EXISTS bugs (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique bug identifier',
    title VARCHAR(255) NOT NULL COMMENT 'Bug title/summary',
    description TEXT NOT NULL COMMENT 'Detailed bug description',
    priority ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium' COMMENT 'Bug severity level',
    status ENUM('todo', 'in_progress', 'done') NOT NULL DEFAULT 'todo' COMMENT 'Current bug status',
    reporter_id INT NOT NULL COMMENT 'User who reported the bug',
    assignee_id INT NULL COMMENT 'Developer assigned to fix (NULL if unassigned)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Bug creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last modification timestamp',
    
    -- Foreign keys
    CONSTRAINT fk_bug_reporter FOREIGN KEY (reporter_id) 
        REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_bug_assignee FOREIGN KEY (assignee_id) 
        REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    -- Indexes for common queries
    INDEX idx_status (status),
    INDEX idx_reporter (reporter_id),
    INDEX idx_assignee (assignee_id),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at),
    
    -- Constraints
    CONSTRAINT chk_priority CHECK (priority IN ('low', 'medium', 'high', 'critical')),
    CONSTRAINT chk_status CHECK (status IN ('todo', 'in_progress', 'done'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bug reports and development tasks';

-- ==========================================
-- SAMPLE DATA (Optional - Remove in production)
-- ==========================================
-- Uncomment below to insert sample data
-- INSERT INTO users (name, email, password, role) VALUES
-- ('Admin User', 'admin@jiralite.com', '$2y$10$YourHashedPasswordHere', 'admin'),
-- ('John Developer', 'john@jiralite.com', '$2y$10$YourHashedPasswordHere', 'developer'),
-- ('Jane QA', 'jane@jiralite.com', '$2y$10$YourHashedPasswordHere', 'qa');

-- ==========================================
-- DATABASE CONFIGURATION
-- ==========================================
-- Character set: UTF-8 Unicode (supports emojis and all languages)
-- Collation: utf8mb4_unicode_ci (case-insensitive Unicode)
-- Engine: InnoDB (ACID compliance, foreign key support)
-- =========================================
