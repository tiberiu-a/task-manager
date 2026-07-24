CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    birthday DATE
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    start_date DATETIME,
    due_date DATETIME,
    finished_date DATETIME,
    time_estimation INT,
    time_spent INT,
    priority ENUM('low', 'medium', 'high'),
    status ENUM('to_do', 'in_progress', 'done'),
    creator_id INT NOT NULL,
    FOREIGN KEY (creator_id) REFERENCES users(id),
    assignee_id INT,    
    FOREIGN KEY (assignee_id) REFERENCES users(id)
);