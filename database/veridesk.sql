CREATE DATABASE IF NOT EXISTS veridesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE veridesk;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS activity_logs,notifications,documents,applications,users;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, role ENUM('candidate','admin') NOT NULL DEFAULT 'candidate', phone VARCHAR(30) NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_users_role(role)
) ENGINE=InnoDB;
CREATE TABLE applications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL UNIQUE,
 reference_no VARCHAR(24) NOT NULL UNIQUE, status ENUM('Not Submitted','Submitted','Under Review','Action Required','Completed') NOT NULL DEFAULT 'Not Submitted',
 submitted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_app_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_app_status(status), INDEX idx_submitted(submitted_at)
) ENGINE=InnoDB;
CREATE TABLE documents (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, application_id BIGINT UNSIGNED NOT NULL,
 category ENUM('Identity Proof','Educational Certificate','Address Proof','Reference Document') NOT NULL,
 original_name VARCHAR(255) NOT NULL, stored_name VARCHAR(100) NOT NULL UNIQUE, mime_type VARCHAR(100) NOT NULL, file_size BIGINT UNSIGNED NOT NULL,
 status ENUM('Pending','Approved','Rejected','Resubmission Required') NOT NULL DEFAULT 'Pending', admin_remarks TEXT NULL,
 uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, reviewed_at DATETIME NULL,
 CONSTRAINT fk_doc_app FOREIGN KEY(application_id) REFERENCES applications(id) ON DELETE CASCADE,
 INDEX idx_doc_status(status), INDEX idx_doc_app_category(application_id,category)
) ENGINE=InnoDB;
CREATE TABLE notifications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL, title VARCHAR(160) NOT NULL, body VARCHAR(500) NOT NULL,
 related_id BIGINT UNSIGNED NULL, is_read TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_not_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_not_user_read(user_id,is_read), INDEX idx_not_created(created_at)
) ENGINE=InnoDB;
CREATE TABLE activity_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NULL, action VARCHAR(80) NOT NULL, related_id BIGINT UNSIGNED NULL,
 description VARCHAR(500) NOT NULL DEFAULT '', ip_address VARCHAR(45) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_log_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL, INDEX idx_log_action(action), INDEX idx_log_created(created_at), INDEX idx_log_user(user_id)
) ENGINE=InnoDB;
