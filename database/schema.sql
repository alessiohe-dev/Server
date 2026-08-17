CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(32) NOT NULL,
    password VARCHAR(255) NOT NULL,
    level INT NOT NULL DEFAULT 1,
    experience BIGINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_username_unique (username)
);

CREATE TABLE IF NOT EXISTS progress (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    level_id VARCHAR(64) NOT NULL,
    darts_thrown INT UNSIGNED NOT NULL DEFAULT 0,
    successful_hits INT UNSIGNED NOT NULL DEFAULT 0,
    accuracy DECIMAL(6,2) NOT NULL DEFAULT 0,
    attempts INT UNSIGNED NOT NULL DEFAULT 0,
    completed TINYINT(1) NOT NULL DEFAULT 0,
    completed_at DATETIME NULL,
    last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY progress_user_level_unique (user_id, level_id),
    KEY progress_user_index (user_id)
);

CREATE TABLE IF NOT EXISTS level_rewards (
    level_id VARCHAR(64) NOT NULL,
    experience INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (level_id)
);

INSERT INTO level_rewards (level_id, experience) VALUES ('0001', 1000), ('0002', 1000)
ON DUPLICATE KEY UPDATE experience = VALUES(experience);

CREATE TABLE IF NOT EXISTS highscores (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    level_id VARCHAR(64) NOT NULL,
    score BIGINT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY highscores_level_score_index (level_id, score DESC),
    KEY highscores_user_index (user_id)
);

CREATE TABLE IF NOT EXISTS licenses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    license_key VARCHAR(64) NOT NULL,
    key_hash CHAR(64) NOT NULL,
    customer_name VARCHAR(160) NOT NULL,
    customer_email VARCHAR(254) NULL,
    device_id VARCHAR(255) NOT NULL,
    license_type VARCHAR(32) NOT NULL DEFAULT 'full',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    expires_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY licenses_key_unique (license_key),
    UNIQUE KEY licenses_hash_unique (key_hash)
);

CREATE TABLE IF NOT EXISTS support_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(160) NOT NULL,
    email VARCHAR(254) NOT NULL,
    topic VARCHAR(80) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY support_status_created_index (status, created_at)
);
