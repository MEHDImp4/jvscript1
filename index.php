<?php
session_start();
require_once "pdo.php";
require_once "util.php";
?>
<!DOCTYPE html>
<html>

<head>
    <title>Diouri Mehdi 57b47596 - Resume Registry</title>
    <?php require_once "head.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Mehdi's Resume Registry</h1>
        <?php
        flashMessages();

        if (isset($_SESSION['user_id'])) {
            echo '<p><a href="logout.php">Logout</a></p>' . "\n";
        } else {
            echo '<p><a href="login.php">Please log in</a></p>' . "\n";
        }

        $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline FROM Profile");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            echo ('<table class="table">' . "\n");
            echo ('<thead><tr><th>Name</th><th>Headline</th>');
            if (isset($_SESSION['user_id'])) {
                echo ('<th>Action</th>');
            }
            echo ('</tr></thead><tbody>' . "\n");
            foreach ($rows as $row) {
                echo "<tr><td>";
                echo ('<a href="view.php?profile_id=' . $row['profile_id'] . '">');
                echo (htmlentities($row['first_name']) . " " . htmlentities($row['last_name']));
                echo ('</a>');
                echo ("</td><td>");
                echo (htmlentities($row['headline']));
                echo ("</td>");
                if (isset($_SESSION['user_id'])) {
                    echo ("<td>");
                    echo ('<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> / ');
                    echo ('<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>');
                    echo ("</td>");
                }
                echo ("</tr>\n");
            }
            echo ("</tbody></table>\n");
        } else {
            echo "<p>No rows found</p>\n";
        }

        if (isset($_SESSION['user_id'])) {
            echo '<p><a href="add.php">Add New Entry</a></p>' . "\n";
        }
        ?>
    </div>
</body>

</html>