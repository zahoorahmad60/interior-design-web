CREATE DATABASE IF NOT EXISTS interior_design2;

USE interior_design2;

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
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    notification_sent BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);
CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    designer_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5), -- Assuming a 1 to 5 rating scale
    comments TEXT,
    feedback_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);
ALTER TABLE feedback DROP FOREIGN KEY feedback_ibfk_1;

ALTER TABLE feedback DROP COLUMN order_id;



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
    order_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    date DATE,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
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
    sender_role ENUM('client', 'designer') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Table: evaluations
CREATE TABLE evaluations (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    client_id INT NOT NULL,
    designer_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback TEXT,
    evaluation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (client_id) REFERENCES user(user_id),
    FOREIGN KEY (designer_id) REFERENCES interior_designer(designer_id)
);

-- Dummy users
INSERT INTO user (email, location, password, username, preferences, role) VALUES
('client1@example.com', 'New York, NY', '$2y$10$abcdefghijk1234567890lmnopqrstuv', 'client1', 'Modern, Minimalist', 'client'),
('client2@example.com', 'Los Angeles, CA', '$2y$10$qrstuvwxyz1234567890lmnopqrstuv', 'client2', 'Vintage, Rustic', 'client'),
('designer1@example.com', 'San Francisco, CA', '$2y$10$lmnopqrstuv1234567890abcdefghijk', 'designer1', 'Contemporary, Modern', 'designer');

-- Dummy interior designers
INSERT INTO interior_designer (user_id, name, experience) VALUES
(3, 'Designer A', '5 years');

-- Dummy projects
INSERT INTO projects (designer_id, title, description, image, date_created) VALUES
(1, 'Modern Living Room', 'A modern living room design.', 'modern_living_room.jpg', '2023-05-01'),
(1, 'Rustic Kitchen', 'A rustic kitchen design.', 'rustic_kitchen.jpg', '2023-06-15');

-- Dummy orders
INSERT INTO orders (user_id, designer_id, duration, order_details, amount, status, notification_sent) VALUES
(1, 1, 30, 'Design a modern living room.', 1000.00, 'pending', FALSE),
(2, 1, 45, 'Create a rustic kitchen layout.', 1500.00, 'pending', FALSE);

-- Dummy payments
INSERT INTO payments (user_id, order_id, amount, date) VALUES
(1, 1, 1000.00, '2024-01-15'),
(2, 2, 1500.00, '2024-01-16');

-- Dummy evaluations
INSERT INTO evaluations (order_id, client_id, designer_id, rating, feedback, evaluation_date) VALUES
(1, 1, 1, 5, 'Excellent design and quick delivery!', '2024-01-20'),
(2, 2, 1, 4, 'Great design but slightly delayed.', '2024-01-25');

-- Dummy chats
INSERT INTO chats (user_id, designer_id, message, sender_role) VALUES
(1, 1, 'Can you make the design more minimalist?', 'client'),
(1, 1, 'Sure, I will adjust it for you.', 'designer'),
(2, 1, 'I need more details on the rustic kitchen.', 'client');

-- Dummy messages
INSERT INTO messages (sender_id, receiver_id, message) VALUES
(1, 3, 'Looking forward to the modern living room design.'),
(3, 1, 'I will get started on it soon.'),
(2, 3, 'Can you give me a timeline for the kitchen design?');

-- Dummy wishlist
INSERT INTO wishlist (user_id, name, price) VALUES
(1, 'Modern Sofa', 500.00),
(2, 'Rustic Dining Table', 750.00);

-- Dummy design ideas
INSERT INTO design_idea (designer_id, title, description, date_created, image) VALUES
(1, 'Living Room Modern Design', 'A sleek modern design for living rooms.', '2023-05-01', 'image1.jpg'),
(1, 'Rustic Kitchen Design', 'A charming rustic design for kitchens.', '2023-06-15', 'image2.jpg');

-- Dummy interactions
INSERT INTO interact (user_id, designer_id) VALUES
(1, 1),
(2, 1);

-- Dummy choices
INSERT INTO choose (user_id, idea_id) VALUES
(1, 1),
(2, 2);

ALTER TABLE orders MODIFY status ENUM('pending', 'accepted', 'rejected', 'completed') NOT NULL;

