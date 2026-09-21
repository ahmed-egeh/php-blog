CREATE TABLE IF NOT EXISTS users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  username varchar(200) NOT NULL,
  email varchar(200) NOT NULL,
  password varchar(250) NOT NULL,
  first_name varchar(200) NOT NULL,
  last_name varchar(200) NOT NULL,
  last_login timestamp NULL,
  activated tinyint(1) DEFAULT 0,
  user_image varchar(250) NULL,
  created_at timestamp NULL DEFAULT current_timestamp(),
  updated_at timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  deleted_at timestamp NULL DEFAULT NULL,
  UNIQUE KEY unique_email (email),
  UNIQUE KEY unique_username (username),
  INDEX `index_email` (`email`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE users
ADD activation_token VARCHAR(64) NULL AFTER user_image,
ADD activation_expires_at DATETIME NULL AFTER activation_token;