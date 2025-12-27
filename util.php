<?php
// util.php - Utility functions

function flashSet($message)
{
    $_SESSION['flash'] = $message;
}

function flashGet()
{
    if (isset($_SESSION['flash'])) {
        echo ('<p style="color: green;">' . htmlentities($_SESSION['flash']) . "</p>\n");
        unset($_SESSION['flash']);
    }
    if (isset($_SESSION['error'])) {
        echo ('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
    }
}

function validateProfile()
{
    if (
        strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0 ||
        strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0 ||
        strlen($_POST['summary']) == 0
    ) {
        return "All fields are required";
    }
    if (strpos($_POST['email'], '@') === false) {
        return "Email address must contain @";
    }
    return true;
}

function validatePos()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i]))
            continue;
        if (!isset($_POST['desc' . $i]))
            continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        if (strlen($year) == 0 || strlen($desc) == 0) {
            return "All fields are required";
        }
        if (!is_numeric($year)) {
            return "Position year must be numeric";
        }
    }
    return true;
}

function validateEdu()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i]))
            continue;
        if (!isset($_POST['edu_school' . $i]))
            continue;
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];
        if (strlen($year) == 0 || strlen($school) == 0) {
            return "All fields are required";
        }
        if (!is_numeric($year)) {
            return "Education year must be numeric";
        }
    }
    return true;
}

function getOrCreateInstitution($pdo, $name)
{
    $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
    $stmt->execute(array(':name' => $name));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false)
        return $row['institution_id'];

    $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
    $stmt->execute(array(':name' => $name));
    return $pdo->lastInsertId();
}

function insertPositions($pdo, $profile_id)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i]))
            continue;
        if (!isset($_POST['desc' . $i]))
            continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];

        $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
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
}

function insertEducations($pdo, $profile_id)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i]))
            continue;
        if (!isset($_POST['edu_school' . $i]))
            continue;
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];

        $institution_id = getOrCreateInstitution($pdo, $school);

        $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year)
            VALUES ( :pid, :iid, :rank, :year)');
        $stmt->execute(
            array(
                ':pid' => $profile_id,
                ':iid' => $institution_id,
                ':rank' => $rank,
                ':year' => $year
            )
        );
        $rank++;
    }
}
