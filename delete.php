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

if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute([
        ':pid' => $profile_id,
        ':uid' => $_SESSION['user_id']
    ]);
    redirectWithMessage('index.php', 'success', 'Profile deleted');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mehdi - Delete Profile</title>
</head>
<body>
    <h1>Delete Profile</h1>
    <?php echo flashMessages(); ?>
    <p>Are you sure you want to delete this profile?</p>
    <p>Name: <?php echo htmlentities($row['first_name'] . ' ' . $row['last_name']); ?></p>
    <form method="POST">
        <input type="hidden" name="profile_id" value="<?php echo htmlentities($row['profile_id']); ?>">
        <input type="submit" name="delete" value="Delete">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
