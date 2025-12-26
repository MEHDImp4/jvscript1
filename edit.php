<!DOCTYPE html>
<html>
<head>
<title>Md Mehdi Hasan - Edit Profile</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<h1>Editing Profile for Md Mehdi Hasan</h1>
<?php
if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
}
if ( ! isset($_GET['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :xyz AND user_id = :uid");
$stmt->execute(array(":xyz" => $_GET['profile_id'], ":uid" => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = "Bad value for profile_id";
    header("Location: index.php");
    return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];
$positions = loadPos($pdo, $profile_id);
$educations = loadEdu($pdo, $profile_id);
if ( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
}
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {
    $msg = validateProfile();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=$profile_id");
        return;
    }
    $msg = validatePos();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=$profile_id");
        return;
    }
    $msg = validateEdu();
    if ( is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=$profile_id");
        return;
    }
    $stmt = $pdo->prepare('UPDATE Profile SET first_name=:fn, last_name=:ln, email=:em, headline=:he, summary=:su WHERE profile_id=:pid AND user_id=:uid');
    $stmt->execute(array(
        ':pid' => $profile_id,
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    // Clear old positions and educations
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $profile_id));
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $profile_id));
    insertPositions($pdo, $profile_id);
    insertEducations($pdo, $profile_id);
    $_SESSION['success'] = "Profile updated";
    header("Location: index.php");
    return;
}
?>
<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60" value="<?php echo $fn; ?>"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60" value="<?php echo $ln; ?>"/></p>
<p>Email:
<input type="text" name="email" size="30" value="<?php echo $em; ?>"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80" value="<?php echo $hl; ?>"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"><?php echo $su; ?></textarea></p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
<?php
$countPos = 0;
foreach ($positions as $pos) {
    $countPos++;
    echo '<div id="position'.$countPos.'">';
    echo '<p>Year: <input type="text" name="year'.$countPos.'" value="'.htmlentities($pos['year']).'" />';
    echo ' <input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>';
    echo '<textarea name="desc'.$countPos.'" rows="8" cols="80">'.htmlentities($pos['description']).'</textarea>';
    echo '</div>';
}
?>
</div>
</p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="edu_fields">
<?php
$countEdu = 0;
foreach ($educations as $edu) {
    $countEdu++;
    echo '<div id="edu'.$countEdu.'">';
    echo '<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.htmlentities($edu['year']).'" />';
    echo ' <input type="button" value="-" onclick="$(\'#edu'.$countEdu.'\').remove();return false;"></p>';
    echo '<p>School: <input type="text" name="edu_school'.$countEdu.'" class="school" value="'.htmlentities($edu['name']).'" /></p>';
    echo '</div>';
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
countPos = <?php echo $countPos; ?>;
countEdu = <?php echo $countEdu; ?>;
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('.school').autocomplete({
        source: "school.php"
    });
    $('#addPos').click(function(event){
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
            </div>');
    });
    $('#addEdu').click(function(event){
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine education entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);
        $('#edu_fields').append(
            '<div id="edu'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p> \
            <p>School: <input type="text" name="edu_school'+countEdu+'" class="school" value="" /></p> \
            </div>');
        $('.school').autocomplete({
            source: "school.php"
        });
    });
});
</script>
</div>
</body>
</html>