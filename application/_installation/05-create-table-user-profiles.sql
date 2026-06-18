CREATE TABLE IF NOT EXISTS user_profiles (
    user_id INT NOT NULL,
    bio TEXT NULL,
    location VARCHAR(100) NULL,
    hobby VARCHAR(100) NULL,
    birthday DATE NULL,
    profile_picture_filename VARCHAR(255) NULL,
    cover_picture_filename VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;