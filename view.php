<?php
require_once 'bootstrap.php';

$profile_id = $_GET['profile_id'] ?? null;
if ($profile_id === null) {
    redirectWithMessage('index.php', 'error', 'Missing profile_id');
}

$row = loadProfile($pdo, $profile_id);
if ($row === false) {
    redirectWithMessage('index.php', 'error', 'Profile not found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mehdi - Profile View</title>
</head>
<body>
    <h1>Profile Information</h1>
    <?php echo flashMessages(); ?>
    <p>First Name: <?php echo htmlentities($row['first_name']); ?></p>
    <p>Last Name: <?php echo htmlentities($row['last_name']); ?></p>
    <p>Email: <?php echo htmlentities($row['email']); ?></p>
    <p>Headline:</p>
    <p><?php echo htmlentities($row['headline']); ?></p>
    <p>Summary:</p>
    <p><?php echo nl2br(htmlentities($row['summary'])); ?></p>
    <p><a href="index.php">Done</a></p>
</body>
</html>
