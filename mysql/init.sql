-- Membuat tabel contoh
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);

-- Menambahkan data contoh
INSERT INTO users (name, email) VALUES ('arya', 'arya@example.com');
