


<?php
require_once 'bootstrap.php';

$stmt = $pdo->query('SELECT profile_id, user_id, first_name, last_name, headline FROM Profile ORDER BY last_name, first_name');
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mehdi - Profile Database</title>
</head>
<body>
    <h1>Profile Database</h1>
    <?php echo flashMessages(); ?>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <p><a href="login.php">Please log in</a></p>
    <?php else: ?>
        <p><a href="logout.php">Logout</a></p>
    <?php endif; ?>

    <?php
    $profiles = [];
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->query('SELECT profile_id, user_id, first_name, last_name, headline FROM Profile ORDER BY last_name, first_name');
        $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>

    <?php if (isset($_SESSION['user_id']) && count($profiles) > 0): ?>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Headline</th>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <th>Action</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($profiles as $row): ?>
                <tr>
                    <td>
                        <a href="view.php?profile_id=<?php echo htmlentities($row['profile_id']); ?>">
                            <?php echo htmlentities($row['first_name'] . ' ' . $row['last_name']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlentities($row['headline']); ?></td>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <td>
                            <?php if ($row['user_id'] == $_SESSION['user_id']): ?>
                                <a href="edit.php?profile_id=<?php echo htmlentities($row['profile_id']); ?>">Edit</a>
                                <a href="delete.php?profile_id=<?php echo htmlentities($row['profile_id']); ?>">Delete</a>
                            <?php else: ?>
                                &mdash;
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No rows found</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p><a href="add.php">Add New Entry</a></p>
    <?php endif; ?>
</body>
</html>
