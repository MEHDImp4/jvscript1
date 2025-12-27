<?php
ob_start();
session_start();
require_once "pdo.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
}

if (isset($_POST['cancel'])) {
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

$stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :prof AND user_id = :uid');
$stmt->execute(array(':prof' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false) {
    $_SESSION['error'] = 'Could not load profile';
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

if (
    isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])
) {

    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        ob_end_clean();
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id'], true, 303);
        exit();
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        ob_end_clean();
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id'], true, 303);
        exit();
    }

    $msg = validateEdu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        ob_end_clean();
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id'], true, 303);
        exit();
    }

    $stmt = $pdo->prepare('UPDATE Profile SET 
        first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
        WHERE profile_id = :pid AND user_id = :uid');
    $stmt->execute(
        array(
            ':pid' => $_REQUEST['profile_id'],
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        )
    );

    // Clear out the old positions
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    // Insert the position entries
    insertPositions($pdo, $_REQUEST['profile_id']);

    // Clear out the old educations
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    // Insert the education entries
    insertEducations($pdo, $_REQUEST['profile_id']);

    $_SESSION['flash'] = 'Profile updated';
    ob_end_clean();
    header('Location: index.php', true, 303);
    exit();
}

$positions = array();
$stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
$stmt->execute(array(':prof' => $_REQUEST['profile_id']));
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $positions[] = $row;
}

$schools = array();
$stmt = $pdo->prepare('SELECT * FROM Education JOIN Institution ON Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank');
$stmt->execute(array(':prof' => $_REQUEST['profile_id']));
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $schools[] = $row;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mehdi 57b47596 - Edit Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div class="container">
        <h1>Editing Profile for <?php echo (htmlentities($_SESSION['name'])); ?></h1>
        <?php flashGet(); ?>
        <form method="post" action="edit.php">
            <input type="hidden" name="profile_id" value="<?= htmlentities($_REQUEST['profile_id']); ?>" />
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?= htmlentities($profile['first_name']); ?>" />
            </p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?= htmlentities($profile['last_name']); ?>" />
            </p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?= htmlentities($profile['email']); ?>" />
            </p>
            <p>Headline:<br />
                <input type="text" name="headline" size="80" value="<?= htmlentities($profile['headline']); ?>" />
            </p>
            <p>Summary:<br />
                <textarea name="summary" rows="8" cols="80"><?= htmlentities($profile['summary']); ?></textarea>
            </p>

            <p>
                Education: <input type="submit" id="addEdu" value="+">
            <div id="edu_fields">
                <?php
                $countEdu = 0;
                if (count($schools) > 0) {
                    foreach ($schools as $school) {
                        $countEdu++;
                        echo ('<div id="edu' . $countEdu . '">');
                        echo ('<p>Year: <input type="text" name="edu_year' . $countEdu . '" value="' . htmlentities($school['year']) . '" /> ');
                        echo ('<input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();return false;"></p>');
                        echo ('<p>School: <input type="text" size="80" name="edu_school' . $countEdu . '" class="school" value="' . htmlentities($school['name']) . '" />');
                        echo ("\n</div>\n");
                    }
                }
                ?>
            </div>
            </p>

            <p>
                Position: <input type="submit" id="addPos" value="+">
            <div id="position_fields">
                <?php
                $countPos = 0;
                if (count($positions) > 0) {
                    foreach ($positions as $position) {
                        $countPos++;
                        echo ('<div id="position' . $countPos . '">');
                        echo ('<p>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '" /> ');
                        echo ('<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"></p>');
                        echo ('<textarea name="desc' . $countPos . '" rows="8" cols="80">' . "\n");
                        echo (htmlentities($position['description']) . "\n");
                        echo ("\n</textarea>\n</div>\n");
                    }
                }
                ?>
            </div>
            </p>

            <p>
                <input type="submit" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </p>
        </form>
        <script>
            countPos = <?= $countPos ?>;
            countEdu = <?= $countEdu ?>;

            $(document).ready(function () {
                window.console && console.log('Document ready called');
                $('#addPos').click(function (event) {
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position " + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year'+ countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+ countPos + '\').remove();return false;"></p> \
            <textarea name="desc'+ countPos + '" rows="8" cols="80"></textarea> \
            </div>');
                });

                $('#addEdu').click(function (event) {
                    event.preventDefault();
                    if (countEdu >= 9) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEdu++;
                    window.console && console.log("Adding education " + countEdu);

                    $('#edu_fields').append(
                        '<div id="edu' + countEdu + '"> \
            <p>Year: <input type="text" name="edu_year'+ countEdu + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+ countEdu + '\').remove();return false;"></p> \
            <p>School: <input type="text" size="80" name="edu_school'+ countEdu + '" class="school" value="" /> \
            </p></div>'
                    );

                    $('.school').autocomplete({
                        source: "school.php"
                    });

                });

                $('.school').autocomplete({
                    source: "school.php"
                });

            });
        </script>
    </div>
</body>

</html>