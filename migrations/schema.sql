CREATE DATABASE flight_booking;

USE flight_booking;

CREATE TABLE users
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(100) UNIQUE NOT NULL,
    password   VARCHAR(255)        NOT NULL,
    name       VARCHAR(100)        NOT NULL,
    role       ENUM('admin', 'user') DEFAULT 'user',
    balance    DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE flights
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    flight_number VARCHAR(20)    NOT NULL,
    price         DECIMAL(10, 2) NOT NULL,
    seats         INT            NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE schedules
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    destination    VARCHAR(100) NOT NULL,
    flight_id      INT          NOT NULL,
    departure_time DATETIME     NOT NULL,
    FOREIGN KEY (flight_id) REFERENCES flights (id) ON DELETE CASCADE
);

CREATE TABLE bookings
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    flight_id   INT NOT NULL,
    schedule_id INT NOT NULL,
    seats       INT NOT NULL,
    status      ENUM('pending', 'confirmed', 'canceled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (flight_id) REFERENCES flights (id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules (id) ON DELETE CASCADE
);

CREATE TABLE logs
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    action     VARCHAR(255) NOT NULL,
    user_id    INT          NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
);

INSERT INTO users (email, password, name, role)
VALUES ('admin@email.com', MD5('Admin@123'), 'Admin', 'admin');