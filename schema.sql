CREATE TABLE IF NOT EXISTS users (
    user_id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR(128),
    email VARCHAR(128),
    password VARCHAR(128),
    PRIMARY KEY(user_id),
    INDEX(email),
    INDEX(password)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Profile (
    profile_id INTEGER NOT NULL AUTO_INCREMENT,
    user_id INTEGER,
    first_name VARCHAR(128),
    last_name VARCHAR(128),
    email VARCHAR(128),
    headline VARCHAR(256),
    summary TEXT,
    PRIMARY KEY(profile_id)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Institution (
    institution_id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR(255),
    PRIMARY KEY(institution_id),
    UNIQUE(name)
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Position (
    position_id INTEGER NOT NULL AUTO_INCREMENT,
    profile_id INTEGER,
    rank INTEGER,
    year INTEGER,
    description TEXT,
    PRIMARY KEY(position_id),
    CONSTRAINT position_ibfk_1 
        FOREIGN KEY (profile_id) 
        REFERENCES Profile (profile_id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE IF NOT EXISTS Education (
    profile_id INTEGER,
    institution_id INTEGER,
    rank INTEGER,
    year INTEGER,
    PRIMARY KEY(profile_id, institution_id),
    CONSTRAINT education_ibfk_1 
        FOREIGN KEY (profile_id) 
        REFERENCES Profile (profile_id) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT education_ibfk_2 
        FOREIGN KEY (institution_id) 
        REFERENCES Institution (institution_id) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARSET=utf8;

-- Insert default users if they don't exist is handled in pdo.php
-- Insert default institutions is handled in pdo.php
