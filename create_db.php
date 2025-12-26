<?php
// Create the SQLite database and tables for the resume application

$db_file = 'resume.db';

try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(128),
        email VARCHAR(128) UNIQUE,
        password VARCHAR(255)
    )");

    // Create Profile table
    $pdo->exec("CREATE TABLE IF NOT EXISTS Profile (
        profile_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        first_name TEXT,
        last_name TEXT,
        email TEXT,
        headline TEXT,
        summary TEXT,
        FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE
    )");

    // Create Position table
    $pdo->exec("CREATE TABLE IF NOT EXISTS Position (
        position_id INTEGER PRIMARY KEY AUTOINCREMENT,
        profile_id INTEGER,
        rank INTEGER,
        year INTEGER,
        description TEXT,
        FOREIGN KEY (profile_id) REFERENCES Profile (profile_id) ON DELETE CASCADE
    )");

    // Create Institution table
    $pdo->exec("CREATE TABLE IF NOT EXISTS Institution (
        institution_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) UNIQUE
    )");

    // Create Education table
    $pdo->exec("CREATE TABLE IF NOT EXISTS Education (
        profile_id INTEGER,
        institution_id INTEGER,
        rank INTEGER,
        year INTEGER,
        PRIMARY KEY(profile_id, institution_id),
        FOREIGN KEY (profile_id) REFERENCES Profile (profile_id) ON DELETE CASCADE,
        FOREIGN KEY (institution_id) REFERENCES Institution (institution_id) ON DELETE CASCADE
    )");

    // Insert initial institutions
    $institutions = [
        'University of Michigan',
        'University of Virginia',
        'University of Oxford',
        'University of Cambridge',
        'Stanford University',
        'Duke University',
        'Michigan State University',
        'Mississippi State University',
        'Montana State University'
    ];

    $stmt = $pdo->prepare("INSERT OR IGNORE INTO Institution (name) VALUES (?)");
    foreach ($institutions as $inst) {
        $stmt->execute([$inst]);
    }

    // Insert a sample user for testing
    $pdo->exec("INSERT OR IGNORE INTO users (name, email, password) VALUES ('Test User', 'test@example.com', 'password')");

    echo "Database created successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>