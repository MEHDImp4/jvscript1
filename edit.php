<?php
// Edit an existing profile with positions and education
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

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate all fields
    $profileValid = validateProfile($_POST);
    if ($profileValid !== true) {
        setFlash($profileValid);
        header('Location: edit.php?profile_id=' . $profile_id);
        exit();
    }

    $posValid = validatePositions($_POST);
    if ($posValid !== true) {
        setFlash($posValid);
        header('Location: edit.php?profile_id=' . $profile_id);
        exit();
    }

    $eduValid = validateEducation($_POST);
    if ($eduValid !== true) {
        setFlash($eduValid);
        header('Location: edit.php?profile_id=' . $profile_id);
        exit();
    }

    // Update profile
    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, 
                           headline = :he, summary = :su WHERE profile_id = :pid');
    $stmt->execute([
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $profile_id
    ]);

    // Clear and re-insert positions
    deletePositions($pdo, $profile_id);
    insertPositions($pdo, $profile_id, $_POST);

    // Clear and re-insert education
    deleteEducation($pdo, $profile_id);
    insertEducation($pdo, $profile_id, $_POST);

    setFlash('Profile updated successfully');
    header('Location: view.php?profile_id=' . $profile_id);
    exit();
}

// Fetch existing positions
$stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank');
$stmt->execute([':pid' => $profile_id]);
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing education
$stmt = $pdo->prepare('SELECT e.*, i.name as school_name 
                       FROM Education e 
                       JOIN Institution i ON e.institution_id = i.institution_id
                       WHERE e.profile_id = :pid 
                       ORDER BY e.rank');
$stmt->execute([':pid' => $profile_id]);
$education = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo getHeader('Edit Profile');

$flash = getFlash();
if ($flash) {
    echo '<p class="flash-error">' . htmlEscape($flash) . '</p>';
}
?>

<h1>Edit Profile</h1>

<form method="post">
    <input type="hidden" name="profile_id" value="<?php echo $profile_id; ?>" />

    <h3>Profile Information</h3>
    <div class="form-group">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" class="form-control"
            value="<?php echo htmlEscape($profile['first_name']); ?>" />
    </div>
    <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" class="form-control"
            value="<?php echo htmlEscape($profile['last_name']); ?>" />
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" class="form-control"
            value="<?php echo htmlEscape($profile['email']); ?>" />
    </div>
    <div class="form-group">
        <label for="headline">Headline:</label>
        <input type="text" name="headline" id="headline" class="form-control"
            value="<?php echo htmlEscape($profile['headline']); ?>" />
    </div>
    <div class="form-group">
        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" rows="4"
            class="form-control"><?php echo htmlEscape($profile['summary']); ?></textarea>
    </div>

    <hr>
    <h3>Positions <button type="button" id="addPos" class="btn btn-success btn-sm">+ Add Position</button></h3>
    <div id="positions">
        <?php
        $posCount = 0;
        foreach ($positions as $pos):
            $posCount++;
            ?>
            <div id="position<?php echo $posCount; ?>" style="margin-top:10px;">
                <p>Year: <input type="text" name="year<?php echo $posCount; ?>" size="10"
                        value="<?php echo htmlEscape($pos['year']); ?>" />
                    <input type="button" value="-"
                        onclick="$('#position<?php echo $posCount; ?>').remove();return false;" />
                </p>
                <textarea name="desc<?php echo $posCount; ?>" rows="4" cols="60"
                    class="form-control"><?php echo htmlEscape($pos['description']); ?></textarea>
            </div>
        <?php endforeach; ?>
        <?php if ($posCount == 0): ?>
            <div id="position1">
                <p>Year: <input type="text" name="year1" size="10" value="" />
                    <input type="button" value="-" onclick="$('#position1').remove();return false;" />
                </p>
                <textarea name="desc1" rows="4" cols="60" class="form-control" placeholder="Description"></textarea>
            </div>
        <?php endif; ?>
    </div>

    <hr>
    <h3>Education <button type="button" id="addEdu" class="btn btn-success btn-sm">+ Add Education</button></h3>
    <div id="educations">
        <?php
        $eduCount = 0;
        foreach ($education as $edu):
            $eduCount++;
            ?>
            <div id="education<?php echo $eduCount; ?>" style="margin-top:10px;">
                <p>Year: <input type="text" name="edu_year<?php echo $eduCount; ?>" size="10"
                        value="<?php echo htmlEscape($edu['year']); ?>" />
                    School: <input type="text" name="edu_school<?php echo $eduCount; ?>" size="50" class="school"
                        value="<?php echo htmlEscape($edu['school_name']); ?>" />
                    <input type="button" value="-"
                        onclick="$('#education<?php echo $eduCount; ?>').remove();return false;" />
                </p>
            </div>
        <?php endforeach; ?>
        <?php if ($eduCount == 0): ?>
            <div id="education1">
                <p>Year: <input type="text" name="edu_year1" size="10" value="" />
                    School: <input type="text" name="edu_school1" size="50" class="school" value="" />
                    <input type="button" value="-" onclick="$('#education1').remove();return false;" />
                </p>
            </div>
        <?php endif; ?>
    </div>

    <hr>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="view.php?profile_id=<?php echo $profile_id; ?>" class="btn btn-default">Cancel</a>
</form>

<script>
    var countPos = <?php echo max($posCount, 1); ?>;
    var countEdu = <?php echo max($eduCount, 1); ?>;

    $(document).ready(function () {
        // Add Position
        $('#addPos').click(function (e) {
            e.preventDefault();
            if (countPos >= 9) {
                alert("Maximum 9 positions allowed");
                return;
            }
            countPos++;
            $('#positions').append(
                '<div id="position' + countPos + '" style="margin-top:10px;">' +
                '<p>Year: <input type="text" name="year' + countPos + '" size="10" value="" /> ' +
                '<input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;" /></p>' +
                '<textarea name="desc' + countPos + '" rows="4" cols="60" class="form-control" placeholder="Description"></textarea>' +
                '</div>'
            );
        });

        // Add Education
        $('#addEdu').click(function (e) {
            e.preventDefault();
            if (countEdu >= 9) {
                alert("Maximum 9 education entries allowed");
                return;
            }
            countEdu++;
            $('#educations').append(
                '<div id="education' + countEdu + '" style="margin-top:10px;">' +
                '<p>Year: <input type="text" name="edu_year' + countEdu + '" size="10" value="" /> ' +
                'School: <input type="text" name="edu_school' + countEdu + '" size="50" class="school" value="" /> ' +
                '<input type="button" value="-" onclick="$(\'#education' + countEdu + '\').remove();return false;" /></p>' +
                '</div>'
            );
            // Re-initialize autocomplete for new fields
            $('.school').autocomplete({ source: "school.php" });
        });

        // Initialize autocomplete on school fields
        $('.school').autocomplete({ source: "school.php" });
    });
</script>

<?php echo getFooter(); ?>