-- depokin — skema database ternormalisasi (3NF)
-- Jalankan: /Applications/XAMPP/xamppfiles/bin/mysql -u root < database/schema.sql

CREATE DATABASE IF NOT EXISTS depokin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE depokin;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS article_logs, article_tags, media, articles, tags, locations, categories, users, settings;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor','penulis') NOT NULL DEFAULT 'penulis',
  bio VARCHAR(500) DEFAULT NULL,
  avatar_path VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(100) NOT NULL,
  description VARCHAR(300) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB;

CREATE TABLE locations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL,
  kind ENUM('kecamatan','kelurahan','area') NOT NULL DEFAULT 'area',
  PRIMARY KEY (id),
  UNIQUE KEY uq_locations_slug (slug),
  UNIQUE KEY uq_locations_name (name)
) ENGINE=InnoDB;

CREATE TABLE tags (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL,
  slug VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tags_slug (slug),
  UNIQUE KEY uq_tags_name (name)
) ENGINE=InnoDB;

CREATE TABLE articles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL,
  deck VARCHAR(500) NOT NULL DEFAULT '',
  content MEDIUMTEXT NOT NULL,
  format ENUM('foto','video','teks') NOT NULL DEFAULT 'foto',
  category_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED DEFAULT NULL,
  author_id INT UNSIGNED NOT NULL,
  editor_id INT UNSIGNED DEFAULT NULL,
  status ENUM('draf','ditinjau','revisi','terbit','arsip') NOT NULL DEFAULT 'draf',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  meta_title VARCHAR(200) DEFAULT NULL,
  meta_description VARCHAR(300) DEFAULT NULL,
  event_start DATETIME DEFAULT NULL,
  event_end DATETIME DEFAULT NULL,
  published_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_articles_slug (slug),
  KEY idx_articles_status_pub (status, published_at),
  KEY idx_articles_category (category_id),
  KEY idx_articles_author (author_id),
  KEY idx_articles_featured (is_featured),
  CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES categories (id),
  CONSTRAINT fk_articles_location FOREIGN KEY (location_id) REFERENCES locations (id) ON DELETE SET NULL,
  CONSTRAINT fk_articles_author FOREIGN KEY (author_id) REFERENCES users (id),
  CONSTRAINT fk_articles_editor FOREIGN KEY (editor_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE article_tags (
  article_id INT UNSIGNED NOT NULL,
  tag_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (article_id, tag_id),
  KEY idx_article_tags_tag (tag_id),
  CONSTRAINT fk_at_article FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE,
  CONSTRAINT fk_at_tag FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE media (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id INT UNSIGNED NOT NULL,
  kind ENUM('gambar','video') NOT NULL DEFAULT 'gambar',
  file_path VARCHAR(255) DEFAULT NULL,
  embed_url VARCHAR(500) DEFAULT NULL,
  alt_text VARCHAR(300) DEFAULT NULL,
  caption TEXT DEFAULT NULL,
  credit_name VARCHAR(150) DEFAULT NULL,
  credit_url VARCHAR(500) DEFAULT NULL,
  license VARCHAR(80) DEFAULT NULL,
  license_url VARCHAR(500) DEFAULT NULL,
  poster_path VARCHAR(255) DEFAULT NULL,
  duration_seconds INT UNSIGNED DEFAULT NULL,
  transcript TEXT DEFAULT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  uploaded_by INT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_media_article (article_id, sort_order),
  CONSTRAINT fk_media_article FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE,
  CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE article_logs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  article_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED DEFAULT NULL,
  action ENUM('buat','simpan','submit','revisi','terbit','arsip','koreksi','featured') NOT NULL,
  note TEXT DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_logs_article (article_id, created_at),
  CONSTRAINT fk_logs_article FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE,
  CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
  setting_key VARCHAR(60) NOT NULL,
  setting_value TEXT DEFAULT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB;