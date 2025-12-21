<?php
// View a single profile with positions and education
require_once 'pdo.php';
require_once 'util.php';

// Get profile ID
$profile_id = $_GET['profile_id'] ?? 0;
if ($profile_id < 1) {
    die("Invalid profile ID");
}

// Fetch profile
$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
$stmt->execute([':pid' => $profile_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die("Profile not found");
}

// Fetch positions
$stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank');
$stmt->execute([':pid' => $profile_id]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch education with institution names
$stmt = $pdo->prepare('SELECT e.*, i.name as school_name 
                       FROM Education e 
                       JOIN Institution i ON e.institution_id = i.institution_id
                       WHERE e.profile_id = :pid 
                       ORDER BY e.rank');
$stmt->execute([':pid' => $profile_id]);
$education = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo getHeader($profile['first_name'] . ' ' . $profile['last_name']);
?>

<h1><?php echo htmlEscape($profile['first_name'] . ' ' . $profile['last_name']); ?></h1>

<p><a href="index.php">&lt; Back to Profiles</a></p>

<div class="panel panel-default">
    <div class="panel-heading"><strong>Profile</strong></div>
    <div class="panel-body">
        <p><strong>Email:</strong> <?php echo htmlEscape($profile['email']); ?></p>
        <p><strong>Headline:</strong> <?php echo htmlEscape($profile['headline']); ?></p>
        <p><strong>Summary:</strong><br><?php echo nl2br(htmlEscape($profile['summary'])); ?></p>
    </div>
</div>

<?php if (count($positions) > 0): ?>
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Positions</strong></div>
        <div class="panel-body">
            <?php foreach ($positions as $pos): ?>
                <div style="margin-bottom: 15px;">
                    <strong><?php echo htmlEscape($pos['year']); ?></strong>
                    <p><?php echo nl2br(htmlEscape($pos['description'])); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (count($education) > 0): ?>
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Education</strong></div>
        <div class="panel-body">
            <?php foreach ($education as $edu): ?>
                <div style="margin-bottom: 15px;">
                    <strong><?php echo htmlEscape($edu['year']); ?></strong> -
                    <?php echo htmlEscape($edu['school_name']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isSignedIn() && $_SESSION['user_id'] == $profile['user_id']): ?>
    <p>
        <a href="edit.php?profile_id=<?php echo $profile_id; ?>" class="btn btn-primary">Edit</a>
        <a href="delete.php?profile_id=<?php echo $profile_id; ?>" class="btn btn-danger">Delete</a>
    </p>
<?php endif; ?>

<?php echo getFooter(); ?>