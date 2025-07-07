-- Add image_path column to users table
ALTER TABLE users ADD COLUMN image_path VARCHAR(255) AFTER specialization;
