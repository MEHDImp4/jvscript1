-- Create the SQLite database and tables for the resume application

CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(128),
    email VARCHAR(128) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS Profile (
    profile_id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    first_name TEXT,
    last_name TEXT,
    email TEXT,
    headline TEXT,
    summary TEXT,
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Position (
    position_id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER,
    rank INTEGER,
    year INTEGER,
    description TEXT,
    FOREIGN KEY (profile_id) REFERENCES Profile (profile_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Institution (
    institution_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) UNIQUE
);

CREATE TABLE IF NOT EXISTS Education (
    profile_id INTEGER,
    institution_id INTEGER,
    rank INTEGER,
    year INTEGER,
    PRIMARY KEY(profile_id, institution_id),
    FOREIGN KEY (profile_id) REFERENCES Profile (profile_id) ON DELETE CASCADE,
    FOREIGN KEY (institution_id) REFERENCES Institution (institution_id) ON DELETE CASCADE
);

-- Insert initial institutions
INSERT OR IGNORE INTO Institution (name) VALUES ('University of Michigan');
INSERT OR IGNORE INTO Institution (name) VALUES ('University of Virginia');
INSERT OR IGNORE INTO Institution (name) VALUES ('University of Oxford');
INSERT OR IGNORE INTO Institution (name) VALUES ('University of Cambridge');
INSERT OR IGNORE INTO Institution (name) VALUES ('Stanford University');
INSERT OR IGNORE INTO Institution (name) VALUES ('Duke University');
INSERT OR IGNORE INTO Institution (name) VALUES ('Michigan State University');
INSERT OR IGNORE INTO Institution (name) VALUES ('Mississippi State University');
INSERT OR IGNORE INTO Institution (name) VALUES ('Montana State University');

-- Insert a sample user
INSERT OR IGNORE INTO users (name, email, password) VALUES ('Test User', 'test@example.com', '1a52e17fa899cf40fb04cfc42e6352f1');