<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if (!isset($_SESSION['user_id'])) {
    die("ACCESS DENIED");
}

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    return;
}

if (
    isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])
) {

    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
        return;
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST['profile_id']);
        return;
    }

    $sql = "UPDATE Profile SET first_name = :fn,
            last_name = :ln, email = :em, headline = :he, summary = :su
            WHERE profile_id = :pid AND user_id = :uid";
    $stmt = $pdo->prepare($sql);
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

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position
        WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    // Insert the position entries
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i]))
            continue;
        if (!isset($_POST['desc' . $i]))
            continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];

        $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(
            array(
                ':pid' => $_REQUEST['profile_id'],
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            )
        );
        $rank++;
    }

    $_SESSION['success'] = 'Profile updated';
    header('Location: index.php');
    return;
}

// Guardian: Make sure that profile_id is present
if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row === false) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz ORDER BY rank");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mehdi - Edit Profile</title>
    <?php require_once "head.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Editing Profile for <?php echo htmlentities($_SESSION['name']); ?></h1>
        <?php flashMessages(); ?>
        <form method="post" action="edit.php">
            <input type="hidden" name="profile_id" value="<?php echo $row['profile_id'] ?>">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control"
                value="<?php echo htmlentities($row['first_name']); ?>"><br />
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control"
                value="<?php echo htmlentities($row['last_name']); ?>"><br />
            <label for="email">Email</label>
            <input type="text" name="email" id="email" class="form-control"
                value="<?php echo htmlentities($row['email']); ?>"><br />
            <label for="headline">Headline</label>
            <input type="text" name="headline" id="headline" class="form-control"
                value="<?php echo htmlentities($row['headline']); ?>"><br />
            <label for="summary">Summary</label>
            <textarea name="summary" id="summary" rows="8"
                class="form-control"><?php echo htmlentities($row['summary']); ?></textarea><br />

            <label>Position</label> <input type="submit" id="addPos" value="+" class="btn btn-xs btn-default">
            <div id="position_fields">
                <?php
                $countPos = 0;
                foreach ($positions as $pos) {
                    $countPos++;
                    echo ('<div id="position' . $countPos . '">');
                    echo ('<p>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($pos['year']) . '" />');
                    echo (' <input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"></p>');
                    echo ('<textarea name="desc' . $countPos . '" rows="8" cols="80">' . htmlentities($pos['description']) . '</textarea>');
                    echo ('</div>' . "\n");
                }
                ?>
            </div>

            <input type="submit" value="Save" class="btn btn-primary">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-default">
        </form>

        <script>
            countPos = <?php echo $countPos; ?>;

            $(document).ready(function () {
                console.log('Document ready called');
                $('#addPos').click(function (event) {
                    event.preventDefault();
                    if (countPos >= 9) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    console.log("Adding position " + countPos);
                    $('#position_fields').append(
                        '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year'+ countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+ countPos + '\').remove();return false;"></p> \
            <textarea name="desc'+ countPos + '" rows="8" cols="80"></textarea> \
            </div>');
                });
            });
        </script>
    </div>
</body>

</html>