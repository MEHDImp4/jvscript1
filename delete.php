<?php
session_start();
require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['delete']) && isset($_POST['profile_id'])) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header('Location: index.php');
    return;
}

// Guardian: Make sure that profile_id is present
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT first_name, last_name, profile_id FROM Profile WHERE profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mehdi - Delete Profile</title>
    <?php require_once "head.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Deleteing Profile</h1>
        <p>First Name: <?php echo (htmlentities($row['first_name'])); ?></p>
        <p>Last Name: <?php echo (htmlentities($row['last_name'])); ?></p>
        <form method="post">
            <input type="hidden" name="profile_id" value="<?php echo $row['profile_id'] ?>">
            <input type="submit" value="Delete" name="delete" class="btn btn-danger">
            <a href="index.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</body>

</html>