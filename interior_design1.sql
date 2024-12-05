CREATE DATABASE IF NOT EXISTS interior_design1;

USE interior_design1;

-- Table: user
CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    location VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    preferences TEXT,
    role ENUM('client', 'designer') NOT NULL DEFAULT 'client'
);

-- Table: interior_designer
CREATE TABLE interior_designer (
    designer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    experience TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Table: messages
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(user_id),
    FOREIGN KEY (receiver_id) REFERENCES user(user_id)
);

-- Table: orders
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    designer_id INT NOT NULL,
    duration INT,
    order_details TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10, 2),
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Table: projects
CREATE TABLE projects (
    project_id INT AUTO_INCREMENT PRIMARY KEY,
    designer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    date_created DATE NOT NULL,
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Table: design_idea
CREATE TABLE design_idea (
    idea_id INT AUTO_INCREMENT PRIMARY KEY,
    designer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date_created DATE,
    image TEXT,
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Table: payments
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    date DATE,
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Table: wishlist
CREATE TABLE wishlist (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

-- Table: interact
CREATE TABLE interact (
    user_id INT NOT NULL,
    designer_id INT NOT NULL,
    PRIMARY KEY (user_id, designer_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Table: choose
CREATE TABLE choose (
    user_id INT NOT NULL,
    idea_id INT NOT NULL,
    PRIMARY KEY (user_id, idea_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (idea_id) REFERENCES design_idea(idea_id)
);

-- Table: chats
CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    designer_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Dummy users (hashed passwords)
INSERT INTO user (email, location, password, username, preferences, role) VALUES
('user1@example.com', 'New York, NY', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'user1', 'Modern, Minimalist', 'client'),
('user2@example.com', 'Los Angeles, CA', '$2y$10$qrstuvwxyz1234567890lmnopqrstuv', 'user2', 'Vintage, Rustic', 'client'),
('designer1@example.com', 'San Francisco, CA', '$2y$10$lmnopqrstuv1234567890abcdefghijk', 'designer1', 'Contemporary, Modern', 'designer');

-- Dummy interior designers
INSERT INTO interior_designer (user_id, name, experience) VALUES
(3, 'Designer A', '5 years');

-- Dummy design ideas
INSERT INTO design_idea (designer_id, title, description, date_created, image) VALUES
(1, 'Living Room Modern Design', 'A sleek modern design for living rooms.', '2023-05-01', 'image1.jpg');

-- Dummy payments
INSERT INTO payments (user_id, amount, date) VALUES
(1, 100.00, '2024-01-15'),
(2, 200.00, '2024-01-16');

-- Dummy wishlist items
INSERT INTO wishlist (user_id, name, price) VALUES
(1, 'Modern Sofa', 500.00),
(2, 'Rustic Dining Table', 750.00);

-- Dummy interactions
INSERT INTO interact (user_id, designer_id) VALUES
(1, 1),
(2, 1);

-- Dummy choices
INSERT INTO choose (user_id, idea_id) VALUES
(1, 1),
(2, 1);

-- Dummy projects
INSERT INTO projects (designer_id, title, description, image, date_created) VALUES
(1, 'Modern Living Room', 'A modern living room design', 'modern_living_room.jpg', '2023-05-01'),
(1, 'Rustic Kitchen', 'A rustic kitchen design', 'rustic_kitchen.jpg', '2023-06-15');

ALTER TABLE chats ADD COLUMN sender_role ENUM('client', 'designer') NOT NULL AFTER message;


