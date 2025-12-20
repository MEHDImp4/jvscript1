<?php

$app_name = 'Mehdi Profile Database';

function flashMessages() {
    $out = '';
    if (isset($_SESSION['error'])) {
        $out .= '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        $out .= '<p style="color: green;">' . htmlentities($_SESSION['success']) . "</p>\n";
        unset($_SESSION['success']);
    }
    return $out;
}

function setFlash($key, $message) {
    $_SESSION[$key] = $message;
}

function redirectWithMessage($url, $key, $message) {
    setFlash($key, $message);
    header('Location: ' . $url);
    exit();
}

function validateProfile($data) {
    $fields = ['first_name', 'last_name', 'email', 'headline', 'summary'];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || strlen(trim($data[$field])) === 0) {
            return 'All fields are required';
        }
    }
    if (strpos($data['email'], '@') === false) {
        return 'Email address must contain @';
    }
    return false;
}

function loadProfile(PDO $pdo, $profile_id) {
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        die('Not logged in');
    }
}
