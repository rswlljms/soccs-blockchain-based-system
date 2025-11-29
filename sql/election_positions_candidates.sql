-- Election Positions Table
CREATE TABLE IF NOT EXISTS positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(100) NOT NULL UNIQUE,
    max_votes INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_description (description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Election Candidates Table
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    partylist VARCHAR(100) NOT NULL,
    position_id INT NOT NULL,
    platform TEXT NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_position (position_id),
    INDEX idx_name (lastname, firstname),
    INDEX idx_partylist (partylist)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default positions
INSERT INTO positions (description, max_votes) VALUES
('President', 1),
('Vice President', 1),
('Secretary', 1),
('Treasurer', 1),
('Auditor', 1),
('P.I.O.', 1)
ON DUPLICATE KEY UPDATE description=description;

