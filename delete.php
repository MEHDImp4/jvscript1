<?php
if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
}
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}
$stmt = $pdo->prepare("SELECT first_name, last_name FROM Profile WHERE profile_id = :xyz AND user_id = :uid");
$stmt->execute(array(":xyz" => $_GET['profile_id'], ":uid" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = "Bad value for profile_id";
    header("Location: index.php");
    return;
}
if ( isset($_POST['delete']) ) {
    $stmt = $pdo->prepare("DELETE FROM Profile WHERE profile_id = :xyz AND user_id = :uid");
    $stmt->execute(array(":xyz" => $_GET['profile_id'], ":uid" => $_SESSION['user_id']));
    $_SESSION['success'] = "Profile deleted";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Md Mehdi Hasan - Delete Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
<h1>Delete Profile</h1>
<p>First Name: <?php echo htmlentities($row['first_name']); ?></p>
<p>Last Name: <?php echo htmlentities($row['last_name']); ?></p>
<form method="post">
<input type="hidden" name="profile_id" value="<?php echo $_GET['profile_id']; ?>">
<input type="submit" value="Delete" name="delete">
<input type="submit" value="Cancel" name="cancel">
</form>
</div>
</body>
</html>