CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE users (
    username VARCHAR(16) PRIMARY KEY NOT NULL,
    email VARCHAR(320) UNIQUE NOT NULL,
    password VARCHAR(60) NOT NULL,
    image VARCHAR(255) NOT NULL DEFAULT 'img/default.png',
    username_last_changed TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    role_id INT NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username1 VARCHAR(16) NOT NULL,
    username2 VARCHAR(16) NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (username1, username2),
    FOREIGN KEY (username1) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (username2) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender VARCHAR(16) NOT NULL,
    content VARCHAR(1000) NOT NULL CHECK (content <> ''),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (sender) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE friends (
    username VARCHAR(16) NOT NULL,
    friend VARCHAR(16) NOT NULL,
    status ENUM('pending', 'accepted', 'blocked') NOT NULL,
    PRIMARY KEY(username, friend),
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (friend) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE emailsConfirmations (
    email VARCHAR(320) PRIMARY KEY NOT NULL,
    username VARCHAR(16) NOT NULL,
    hash VARCHAR(64) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE ON UPDATE CASCADE
);

-- root: GRANT SUPER ON *.* TO 'username'@'%';
-- root: FLUSH PRIVILEGES;
-- Enable the MySQL event scheduler
SET GLOBAL event_scheduler = ON;

-- Create an event to delete expired rows every minute
CREATE EVENT delete_expired_rows
ON SCHEDULE EVERY 1 MINUTE
DO
DELETE FROM emailConfirmation WHERE expires_at < NOW();