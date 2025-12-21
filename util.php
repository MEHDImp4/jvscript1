<?php
// Utility functions for the Resume Application

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is signed in. If not, die with ACCESS DENIED.
 */
function checkSignedIn()
{
    if (!isset($_SESSION['user_id'])) {
        die("ACCESS DENIED");
    }
}

/**
 * Check if user is signed in (returns boolean, doesn't die)
 */
function isSignedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Escape HTML entities for safe output
 */
function htmlEscape($str)
{
    return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Set a flash message in the session
 */
function setFlash($message)
{
    $_SESSION['flash'] = $message;
}

/**
 * Get and clear the flash message
 */
function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $msg = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $msg;
    }
    return null;
}

/**
 * Validate profile fields
 * Returns true if valid, or error message string if invalid
 */
function validateProfile($post)
{
    $fields = ['first_name', 'last_name', 'email', 'headline', 'summary'];
    foreach ($fields as $field) {
        if (!isset($post[$field]) || strlen(trim($post[$field])) === 0) {
            return "All fields are required";
        }
    }
    return true;
}

/**
 * Validate position fields (year1-9, desc1-9)
 * Returns true if valid, or error message string if invalid
 */
function validatePositions($post)
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($post['year' . $i]))
            continue;
        if (!isset($post['desc' . $i]))
            continue;

        $year = $post['year' . $i];
        $desc = $post['desc' . $i];

        if (strlen(trim($year)) === 0 || strlen(trim($desc)) === 0) {
            return "All fields are required";
        }

        if (!is_numeric($year)) {
            return "Year must be numeric";
        }
    }
    return true;
}

/**
 * Validate education fields (edu_school1-9, edu_year1-9)
 * Returns true if valid, or error message string if invalid
 */
function validateEducation($post)
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($post['edu_school' . $i]))
            continue;
        if (!isset($post['edu_year' . $i]))
            continue;

        $school = $post['edu_school' . $i];
        $year = $post['edu_year' . $i];

        if (strlen(trim($school)) === 0 || strlen(trim($year)) === 0) {
            return "All fields are required";
        }

        if (!is_numeric($year)) {
            return "Year must be numeric";
        }
    }
    return true;
}

/**
 * Get or create institution ID by name
 */
function getOrCreateInstitution($pdo, $name)
{
    // First try to find existing institution
    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
    $stmt->execute([':name' => $name]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $row['institution_id'];
    }

    // Create new institution
    $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
    $stmt->execute([':name' => $name]);
    return $pdo->lastInsertId();
}

/**
 * Insert positions for a profile
 */
function insertPositions($pdo, $profile_id, $post)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($post['year' . $i]))
            continue;
        if (!isset($post['desc' . $i]))
            continue;

        $year = $post['year' . $i];
        $desc = $post['desc' . $i];

        if (strlen(trim($year)) === 0 && strlen(trim($desc)) === 0)
            continue;

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) 
                               VALUES (:pid, :rank, :year, :desc)');
        $stmt->execute([
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc
        ]);
        $rank++;
    }
}

/**
 * Insert education entries for a profile
 */
function insertEducation($pdo, $profile_id, $post)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($post['edu_school' . $i]))
            continue;
        if (!isset($post['edu_year' . $i]))
            continue;

        $school = $post['edu_school' . $i];
        $year = $post['edu_year' . $i];

        if (strlen(trim($school)) === 0 && strlen(trim($year)) === 0)
            continue;

        $institution_id = getOrCreateInstitution($pdo, $school);

        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year) 
                               VALUES (:pid, :iid, :rank, :year)');
        $stmt->execute([
            ':pid' => $profile_id,
            ':iid' => $institution_id,
            ':rank' => $rank,
            ':year' => $year
        ]);
        $rank++;
    }
}

/**
 * Delete all positions for a profile
 */
function deletePositions($pdo, $profile_id)
{
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);
}

/**
 * Delete all education entries for a profile
 */
function deleteEducation($pdo, $profile_id)
{
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);
}

/**
 * Get the HTML header with Bootstrap and jQuery
 */
function getHeader($title)
{
    return '<!DOCTYPE html>
<html>
<head>
    <title>' . htmlEscape($title) . ' - Mehdi Resume App</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.2.1.js" 
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" 
            integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <style>
        body { padding-top: 20px; }
        .container { max-width: 800px; }
        .flash-error { color: red; margin-bottom: 15px; }
        .flash-success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="container">';
}

/**
 * Get the HTML footer
 */
function getFooter()
{
    return '</div>
</body>
</html>';
}
