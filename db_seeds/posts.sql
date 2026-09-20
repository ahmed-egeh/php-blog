CREATE TABLE IF NOT EXISTS posts (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT NOT NULL,
  title varchar(100) NOT NULL,
  content TEXT NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 0,
  image varchar(150) NULL,
  category_id BIGINT NOT NULL,
  created_at timestamp NULL DEFAULT current_timestamp(),
  updated_at timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  deleted_at timestamp NULL DEFAULT NULL,
  UNIQUE KEY `unique_post` (`user_id`,`title`),
  CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES Users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;