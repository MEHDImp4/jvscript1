<?php
// Delete a profile
require_once 'pdo.php';
require_once 'util.php';

// Must be logged in
checkSignedIn();

// Get profile ID
$profile_id = $_REQUEST['profile_id'] ?? 0;
if ($profile_id < 1) {
    die("Invalid profile ID");
}

// Fetch profile and verify ownership
$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute([':pid' => $profile_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die("Profile not found");
}

if ($profile['user_id'] != $_SESSION['user_id']) {
    die("ACCESS DENIED");
}

// Handle POST - confirm delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete profile (positions and education cascade)
    $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :pid');
    $stmt->execute([':pid' => $profile_id]);

    setFlash('Profile deleted successfully');
    header('Location: index.php');
    exit();
}

echo getHeader('Delete Profile');
?>

<h1>Delete Profile</h1>

<div class="alert alert-danger">
    <p>Are you sure you want to delete this profile?</p>
    <p><strong><?php echo htmlEscape($profile['first_name'] . ' ' . $profile['last_name']); ?></strong></p>
</div>

<form method="post">
    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>" />
    <button type="submit" class="btn btn-danger">Confirm Delete</button>
    <a href="view.php?profile_id=<?php echo $profile_id; ?>" class="btn btn-default">Cancel</a>
</form>

<?php echo getFooter(); ?>