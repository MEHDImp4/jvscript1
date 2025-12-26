<!DOCTYPE html>
<html>
<head>
<title>Md Mehdi Hasan - View Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
<h1>Profile information</h1>
<?php
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = "Bad value for profile_id";
    header("Location: index.php");
    return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
echo "<p>First Name: $fn</p>";
echo "<p>Last Name: $ln</p>";
echo "<p>Email: $em</p>";
echo "<p>Headline: $hl</p>";
echo "<p>Summary: $su</p>";
$positions = loadPos($pdo, $_GET['profile_id']);
if ( count($positions) > 0 ) {
    echo '<p>Position:</p><ul>';
    foreach ($positions as $pos) {
        echo '<li>'.htmlentities($pos['year']).': '.htmlentities($pos['description']).'</li>';
    }
    echo '</ul>';
}
$educations = loadEdu($pdo, $_GET['profile_id']);
if ( count($educations) > 0 ) {
    echo '<p>Education:</p><ul>';
    foreach ($educations as $edu) {
        echo '<li>'.htmlentities($edu['year']).': '.htmlentities($edu['name']).'</li>';
    }
    echo '</ul>';
}
?>
<p><a href="index.php">Done</a></p>
</div>
</body>
</html>