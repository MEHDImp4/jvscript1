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
        header("Location: add.php");
        return;
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    // Data is valid - time to insert
    $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
    VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(
        array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        )
    );
    $profile_id = $pdo->lastInsertId();

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
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            )
        );
        $rank++;
    }

    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mehdi 57b47596 - Add Profile</title>
    <?php require_once "head.php"; ?>
</head>

<body>
    <div class="container">
        <h1>Adding Profile for <?php echo htmlentities($_SESSION['name']); ?></h1>
        <?php flashMessages(); ?>
        <form method="post">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control"><br />
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control"><br />
            <label for="email">Email</label>
            <input type="text" name="email" id="email" class="form-control"><br />
            <label for="headline">Headline</label>
            <input type="text" name="headline" id="headline" class="form-control"><br />
            <label for="summary">Summary</label>
            <textarea name="summary" id="summary" rows="8" class="form-control"></textarea><br />

            <label>Position</label> <input type="submit" id="addPos" value="+" class="btn btn-xs btn-default">
            <div id="position_fields">
            </div>

            <input type="submit" value="Add" class="btn btn-primary">
            <input type="submit" name="cancel" value="Cancel" class="btn btn-default">
        </form>

        <script>
            countPos = 0;

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