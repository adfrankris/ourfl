-- schema.sql (MySQL) for XAMPP/phpMyAdmin
-- Create DB:
--   CREATE DATABASE ourflix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Then import this file.

-- NOTE: This will DROP existing tables.
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS photos;
DROP TABLE IF EXISTS users;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(40) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin') NOT NULL DEFAULT 'admin',
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS photos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(80) NOT NULL,
  caption VARCHAR(280) NULL,
  row_name VARCHAR(30) NOT NULL DEFAULT 'Moments',
  tags VARCHAR(120) NULL,
  file_path VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL,
  created_by INT UNSIGNED NULL,
  PRIMARY KEY (id),
  KEY idx_created_at (created_at),
  KEY idx_row_name (row_name),
  CONSTRAINT fk_photos_user FOREIGN KEY (created_by) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  photo_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  guest_name VARCHAR(60) NULL,
  body VARCHAR(500) NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_photo_id (photo_id),
  KEY idx_created_at (created_at),
  CONSTRAINT fk_comments_photo FOREIGN KEY (photo_id) REFERENCES photos(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
