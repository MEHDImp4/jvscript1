<!DOCTYPE html>
<html>
<head>
<title>Md Mehdi Hasan - Profiles</title>
<?php require_once "pdo.php"; ?>
<?php require_once "util.php"; ?>
</head>
<body>
<div class="container">
<h1>Md Mehdi Diouri's Resume Registry - 57b47596</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}
?>
<p>
<?php
if (!isset($_SESSION['user_id'])) {
    echo '<a href="login.php">Please log in</a>';
} else {
    echo '<a href="logout.php">Logout</a>';
}
?>
</p>
<?php
$stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM Profile ORDER BY first_name");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) > 0) {
    echo '<table border="1">';
    echo '<tr><th>Name</th><th>Headline</th>';
    if (isset($_SESSION['user_id'])) {
        echo '<th>Action</th>';
    }
    echo '</tr>';
    foreach ($rows as $row) {
        echo '<tr>';
        echo '<td><a href="view.php?profile_id='.htmlentities($row['profile_id']).'">'.htmlentities($row['first_name']).' '.htmlentities($row['last_name']).'</a></td>';
        echo '<td>'.htmlentities($row['headline']).'</td>';
        if (isset($_SESSION['user_id'])) {
            echo '<td><a href="edit.php?profile_id='.htmlentities($row['profile_id']).'">Edit</a> <a href="delete.php?profile_id='.htmlentities($row['profile_id']).'">Delete</a></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No profiles found</p>';
}
if (isset($_SESSION['user_id'])) {
    echo '<p><a href="add.php">Add New Entry</a></p>';
}
?>
</div>
</body>
</html>