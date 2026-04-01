-- Tables des utiliseteurs pour l'authentification
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'observer') NOT NULL
);

-- Insérer un utilisateur admin par défaut (username: admin, password: admin123 / username: observer, password: observer123)
INSERT INTO users (username, password_hash, role) VALUES
('admin', '$2y$10$ovpnWQNVxJfCk5vbJT8Jm.P/yQG2LuC0AA/hRAZfMa3ggJQj9044K', 'admin'), -- hash de "admin123"
('observer', '$2y$10$VKxG347J9hlCJn/xQ9FDg.Ls0LQ3DB.yCwbcsuvYguFtKRucihqwW', 'observer'); -- hash de "observer123"
