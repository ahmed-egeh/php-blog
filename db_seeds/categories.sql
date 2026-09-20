CREATE TABLE IF NOT EXISTS categories (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name varchar(80) NOT NULL,
  created_at timestamp NULL DEFAULT current_timestamp(),
  updated_at timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  deleted_at timestamp NULL DEFAULT NULL,
  UNIQUE KEY unique_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

START TRANSACTION;

INSERT INTO categories (name)
SELECT 'Astronomy'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Astronomy'
);

INSERT INTO categories (name)
SELECT 'Planets'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Planets'
);

INSERT INTO categories (name)
SELECT 'Stars'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Stars'
);

INSERT INTO categories (name)
SELECT 'Galaxies'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Galaxies'
);

INSERT INTO categories (name)
SELECT 'Black Holes'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Black Holes'
);

INSERT INTO categories (name)
SELECT 'Space Exploration'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Space Exploration'
);

INSERT INTO categories (name)
SELECT 'Cosmology'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Cosmology'
);

INSERT INTO categories (name)
SELECT 'Exoplanets'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Exoplanets'
);

INSERT INTO categories (name)
SELECT 'Solar System'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Solar System'
);

INSERT INTO categories (name)
SELECT 'Astrobiology'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE name = 'Astrobiology'
);

COMMIT;