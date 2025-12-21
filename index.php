<?php
// Index page - list all profiles
require_once 'pdo.php';
require_once 'util.php';

echo getHeader('Profiles');

$flash = getFlash();
if ($flash) {
    echo '<p class="flash-success">' . htmlEscape($flash) . '</p>';
}
?>

<h1>Resume Registry</h1>

<p>
    <?php if (isSignedIn()): ?>
        Welcome, <?php echo htmlEscape($_SESSION['name']); ?> |
        <a href="logout.php">Logout</a> |
        <a href="add.php">Add New Profile</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</p>

<h2>Profiles</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Headline</th>
            <?php if (isSignedIn()): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query('SELECT profile_id, user_id, first_name, last_name, headline FROM Profile ORDER BY last_name, first_name');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            echo '<td><a href="view.php?profile_id=' . $row['profile_id'] . '">' .
                htmlEscape($row['first_name'] . ' ' . $row['last_name']) . '</a></td>';
            echo '<td>' . htmlEscape($row['headline']) . '</td>';

            if (isSignedIn()) {
                echo '<td>';
                if ($_SESSION['user_id'] == $row['user_id']) {
                    echo '<a href="edit.php?profile_id=' . $row['profile_id'] . '">Edit</a> | ';
                    echo '<a href="delete.php?profile_id=' . $row['profile_id'] . '">Delete</a>';
                }
                echo '</td>';
            }
            echo '</tr>';
        }
        ?>
    </tbody>
</table>

<?php echo getFooter(); ?>