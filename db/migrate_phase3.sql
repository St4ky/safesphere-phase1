-- SafeSphere Phase 3 Migration
-- Run this to upgrade an existing Phase 1/2 database to Phase 3
-- Safe to run multiple times (uses IF NOT EXISTS / MODIFY only when needed)

USE safesphere;

-- 1. Standardize role column to support 'user' | 'admin'
ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'user';

-- 2. Migrate legacy 'Trainee' role to 'user'
UPDATE users SET role = 'user' WHERE role = 'Trainee' OR role IS NULL OR role = '';

-- 3. To make yourself admin, run:
--    UPDATE users SET role = 'admin' WHERE email = 'your@email.com';
