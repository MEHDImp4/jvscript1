<?php
ob_start();
session_start();
require_once "pdo.php";
require_once "util.php";

if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    ob_end_clean();
    header( 'Location: index.php', true, 303);
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz ORDER BY rank");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id where profile_id = :xyz ORDER BY rank");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Mehdi 57b47596 - View Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
</head>
<body>
<div class="container">
<h1>Profile info</h1>
<p>First Name: <?php echo(htmlentities($row['first_name'])); ?></p>
<p>Last Name: <?php echo(htmlentities($row['last_name'])); ?></p>
<p>Email: <?php echo(htmlentities($row['email'])); ?></p>
<p>Headline:<br/>
<?php echo(htmlentities($row['headline'])); ?>
</p>
<p>Summary:<br/>
<?php echo(htmlentities($row['summary'])); ?>
</p>
<p>
<?php
if ( count($positions) > 0 ) {
    echo("Positions\n<ul>\n");
    foreach ( $positions as $pos ) {
        echo("<li>".htmlentities($pos['year']).": ".htmlentities($pos['description'])."</li>\n");
    }
    echo("</ul>\n");
}
if ( count($educations) > 0 ) {
    echo("Education\n<ul>\n");
    foreach ( $educations as $edu ) {
        echo("<li>".htmlentities($edu['year']).": ".htmlentities($edu['name'])."</li>\n");
    }
    echo("</ul>\n");
}
?>
</p>
<a href="index.php">Done</a>
</div>
</body>
</html>
