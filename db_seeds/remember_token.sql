ALTER TABLE users
ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) NULL AFTER activation_expires_at;
