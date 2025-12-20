<?php
// Use a local SQLite database file for simplicity and portability.
// This will create `database.sqlite` in the project root and initialize
// the schema on first run if necessary.

$dbFile = __DIR__ . DIRECTORY_SEPARATOR . 'database.sqlite';
$needInit = !file_exists($dbFile) || filesize($dbFile) === 0;

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Enable foreign key support in SQLite
$pdo->exec('PRAGMA foreign_keys = ON');

if ($needInit) {
		$initSql = <<<'SQL'
PRAGMA foreign_keys = ON;
CREATE TABLE IF NOT EXISTS users (
	user_id INTEGER PRIMARY KEY AUTOINCREMENT,
	name TEXT,
	email TEXT,
	password TEXT
);
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_password ON users(password);

CREATE TABLE IF NOT EXISTS Profile (
	profile_id INTEGER PRIMARY KEY AUTOINCREMENT,
	user_id INTEGER NOT NULL,
	first_name TEXT,
	last_name TEXT,
	email TEXT,
	headline TEXT,
	summary TEXT,
	FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO users (name, email, password)
VALUES ('UMSI', 'umsi@umich.edu', '1a52e17fa899cf40fb04cfc42e6352f1');
SQL;

		// Initialize schema
		$pdo->exec($initSql);
}
