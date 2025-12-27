<?php
ob_start();
session_start();
require_once "pdo.php";
require_once "util.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Adam b083b605 - Resume Registry</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
</head>

<body>
    <div class="container">
        <h1>Mehdi's Resume Registry</h1>
        <?php
        flashGet();

        if (isset($_SESSION['name'])) {
            echo '<p><a href="logout.php">Logout</a></p>';
        } else {
            echo '<p><a href="login.php">Please log in</a></p>';
        }

        $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM Profile");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            echo ('<table class="table table-striped">');
            echo ('<thead><tr><th>Name</th><th>Headline</th>');
            if (isset($_SESSION['name'])) {
                echo ('<th>Action</th>');
            }
            echo ('</tr></thead><tbody>');
            foreach ($rows as $row) {
                echo ('<tr><td>');
                echo ('<a href="view.php?profile_id=' . $row['profile_id'] . '">');
                echo (htmlentities($row['first_name'] . " " . $row['last_name']));
                echo ('</a>');
                echo ('</td><td>');
                echo (htmlentities($row['headline']));
                echo ('</td>');
                if (isset($_SESSION['name'])) {
                    echo ('<td>');
                    echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                    echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                    echo ('</td>');
                }
                echo ("</tr>\n");
            }
            echo ('</tbody></table>');
        } else {
            echo ('<p>No rows found</p>');
        }

        if (isset($_SESSION['name'])) {
            echo ('<p><a href="add.php">Add New Entry</a></p>');
        }
        ?>
    </div>
</body>

</html>