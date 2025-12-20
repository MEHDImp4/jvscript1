<?php
require_once 'bootstrap.php';
requireLogin();

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = validateProfile($_POST);
    if ($error !== false) {
        redirectWithMessage('add.php', 'error', $error);
    }

    $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']
    ]);

    redirectWithMessage('index.php', 'success', 'Profile added');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mehdi - Add Profile</title>
</head>
<body>
    <h1>Add A New Profile</h1>
    <?php echo flashMessages(); ?>
    <form method="POST">
        <p>First Name: <input type="text" name="first_name"></p>
        <p>Last Name: <input type="text" name="last_name"></p>
        <p>Email: <input type="text" name="email"></p>
        <p>Headline: <input type="text" name="headline"></p>
        <p>Summary:</p>
        <p><textarea name="summary" rows="8" cols="80"></textarea></p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
