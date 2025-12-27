<?php
ob_start();
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
}

if (isset($_POST['cancel'])) {
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

$stmt = $pdo->prepare('SELECT first_name, last_name, profile_id FROM Profile WHERE profile_id = :prof AND user_id = :uid');
$stmt->execute(array(':prof' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
    $_SESSION['error'] = 'Could not load profile';
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['flash'] = 'Profile deleted';
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mehdi 57b47596 - Delete Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
</head>

<body>
    <div class="container">
        <h1>Deleteing Profile</h1>
        <form method="post" action="delete.php">
            <p>First Name: <?= htmlentities($row['first_name']) ?></p>
            <p>Last Name: <?= htmlentities($row['last_name']) ?></p>
            <input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
            <input type="submit" value="Delete" name="delete">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </div>
</body>

</html>