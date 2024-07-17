CREATE DATABASE subtitle;

CREATE TABLE subtitle (
                           id INT AUTO_INCREMENT PRIMARY KEY,
                           movieName VARCHAR(255) NOT NULL,
                           fileName VARCHAR(255) NOT NULL,
                           URL VARCHAR(255),
                           file_id VARCHAR(255)
);

CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(255),
                       name VARCHAR(255) NOT NULL,
                       chat_id VARCHAR(255) NOT NULL,
                       admin BOOLEAN DEFAULT FALSE,
                       queue1 VARCHAR(255),
                       queue2 VARCHAR(255),
                       queue3 VARCHAR(255),
                       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

