<?php
require_once 'bootstrap.php';
requireLogin();

$profile_id = $_GET['profile_id'] ?? $_POST['profile_id'] ?? null;
if ($profile_id === null) {
    redirectWithMessage('index.php', 'error', 'Missing profile_id');
}

$row = loadProfile($pdo, $profile_id);
if ($row === false) {
    redirectWithMessage('index.php', 'error', 'Profile not found');
}
if ($row['user_id'] != $_SESSION['user_id']) {
    redirectWithMessage('index.php', 'error', 'Access denied');
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = validateProfile($_POST);
    if ($error !== false) {
        redirectWithMessage('edit.php?profile_id=' . $profile_id, 'error', $error);
    }

    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute([
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $profile_id,
        ':uid' => $_SESSION['user_id']
    ]);

    redirectWithMessage('index.php', 'success', 'Profile updated');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mehdi - Edit Profile</title>
</head>
<body>
    <h1>Edit Profile</h1>
    <?php echo flashMessages(); ?>
    <form method="POST">
        <input type="hidden" name="profile_id" value="<?php echo htmlentities($row['profile_id']); ?>">
        <p>First Name: <input type="text" name="first_name" value="<?php echo htmlentities($row['first_name']); ?>"></p>
        <p>Last Name: <input type="text" name="last_name" value="<?php echo htmlentities($row['last_name']); ?>"></p>
        <p>Email: <input type="text" name="email" value="<?php echo htmlentities($row['email']); ?>"></p>
        <p>Headline: <input type="text" name="headline" value="<?php echo htmlentities($row['headline']); ?>"></p>
        <p>Summary:</p>
        <p><textarea name="summary" rows="8" cols="80"><?php echo htmlentities($row['summary']); ?></textarea></p>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
