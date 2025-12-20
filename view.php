<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz ORDER BY rank");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Mehdi 57b47596 - View Profile</title>
    <?php require_once "head.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Profile information</h1>
        <p>First Name: <?php echo (htmlentities($row['first_name'])); ?></p>
        <p>Last Name: <?php echo (htmlentities($row['last_name'])); ?></p>
        <p>Email: <?php echo (htmlentities($row['email'])); ?></p>
        <p>Headline:<br />
            <?php echo (htmlentities($row['headline'])); ?></p>
        <p>Summary:<br />
            <?php echo (htmlentities($row['summary'])); ?></p>
        <?php
        if (count($positions) > 0) {
            echo ('<p>Position</p><ul>');
            foreach ($positions as $pos) {
                echo ('<li>');
                echo (htmlentities($pos['year']) . ': ' . htmlentities($pos['description']));
                echo ('</li>');
            }
            echo ('</ul>');
        }
        ?>
        <p>
            <a href="index.php">Done</a>
        </p>
    </div>
</body>

</html>