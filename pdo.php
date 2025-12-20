<?php
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'misc';
$user = getenv('DB_USER') ?: 'fred';
$pass = getenv('DB_PASS') ?: 'zap';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
