<?php
// Add a new profile with positions and education
require_once 'pdo.php';
require_once 'util.php';

// Must be logged in
checkSignedIn();

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate all fields
    $profileValid = validateProfile($_POST);
    if ($profileValid !== true) {
        setFlash($profileValid);
        header('Location: add.php');
        exit();
    }

    $posValid = validatePositions($_POST);
    if ($posValid !== true) {
        setFlash($posValid);
        header('Location: add.php');
        exit();
    }

    $eduValid = validateEducation($_POST);
    if ($eduValid !== true) {
        setFlash($eduValid);
        header('Location: add.php');
        exit();
    }

    // Insert profile
    $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) 
                           VALUES (:uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute([
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary']
    ]);
    $profile_id = $pdo->lastInsertId();

    // Insert positions
    insertPositions($pdo, $profile_id, $_POST);

    // Insert education
    insertEducation($pdo, $profile_id, $_POST);

    setFlash('Profile added successfully');
    header('Location: view.php?profile_id=' . $profile_id);
    exit();
}

echo getHeader('Add Profile');

$flash = getFlash();
if ($flash) {
    echo '<p class="flash-error">' . htmlEscape($flash) . '</p>';
}
?>

<h1>Add Profile</h1>

<form method="post">
    <h3>Profile Information</h3>
    <div class="form-group">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" class="form-control" />
    </div>
    <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" class="form-control" />
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" class="form-control" />
    </div>
    <div class="form-group">
        <label for="headline">Headline:</label>
        <input type="text" name="headline" id="headline" class="form-control" />
    </div>
    <div class="form-group">
        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" rows="4" class="form-control"></textarea>
    </div>

    <hr>
    <h3>Positions <button type="button" id="addPos" class="btn btn-success btn-sm">+ Add Position</button></h3>
    <div id="positions">
        <div id="position1">
            <p>Year: <input type="text" name="year1" size="10" value="" />
                <input type="button" value="-" onclick="$('#position1').remove();return false;" />
            </p>
            <textarea name="desc1" rows="4" cols="60" class="form-control" placeholder="Description"></textarea>
        </div>
    </div>

    <hr>
    <h3>Education <button type="button" id="addEdu" class="btn btn-success btn-sm">+ Add Education</button></h3>
    <div id="educations">
        <div id="education1">
            <p>Year: <input type="text" name="edu_year1" size="10" value="" />
                School: <input type="text" name="edu_school1" size="50" class="school" value="" />
                <input type="button" value="-" onclick="$('#education1').remove();return false;" />
            </p>
        </div>
    </div>

    <hr>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-default">Cancel</a>
</form>

<script>
    var countPos = 1;
    var countEdu = 1;

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