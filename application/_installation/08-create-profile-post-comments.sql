CREATE TABLE IF NOT EXISTS profile_post_comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX(post_id),
    INDEX(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;