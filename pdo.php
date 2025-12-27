<?php
// PDO Database Connection & Setup
$pdo_host = "localhost";
$pdo_db = "misc";
$pdo_user = "fred";
$pdo_pass = "zap";
$pdo_port = "3306";

$pdo_dsn = "mysql:host=$pdo_host;port=$pdo_port;dbname=$pdo_db;charset=utf8";

try {
    $pdo = new PDO($pdo_dsn, $pdo_user, $pdo_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if we need to run setup (check for users table)
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {

        // Create tables
        $sql = file_get_contents('schema.sql');
        // Split by semicolon (rough split, but works for this simple schema)
        $queries = explode(';', $sql);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }

        // Insert default users
        $salt = 'php123';
        $hashed = md5($salt);

        $pdo->exec("INSERT INTO users (name, email, password) VALUES 
            ('Chuck', 'csev@umich.edu', '$hashed'),
            ('UMSI', 'umsi@umich.edu', '$hashed')");

        // Insert default institutions
        $pdo->exec("INSERT INTO Institution (name) VALUES 
            ('University of Michigan'),
            ('University of Virginia'),
            ('University of Oxford'),
            ('University of Cambridge'),
            ('Stanford University'),
            ('Duke University'),
            ('Michigan State University'),
            ('Mississippi State University'),
            ('Montana State University')");
    }

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
