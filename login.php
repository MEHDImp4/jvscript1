<!DOCTYPE html>
<html>
<head>
<title>Md Mehdi Hasan - Login</title>
<?php require_once "pdo.php"; ?>
</head>
<body>
<h1>Please Log In</h1>
<?php
if ( isset($_POST['cancel']) ) {
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1'; // php password for 'php123'

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset($_SESSION['user_id']);
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "Email and password are required";
        header("Location: login.php");
        return;
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            // Check if email matches
            $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :em');
            $stmt->execute(array(':em' => $_POST['email']));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row !== false) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['name'] = $row['name'];
                header("Location: index.php");
                return;
            } else {
                $_SESSION['error'] = "Incorrect email";
                header("Location: login.php");
                return;
            }
        } else {
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }
}
?>
<p>
<form method="post">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="pass">Password</label>
<input type="password" name="pass" id="pass"><br/>
<input type="submit" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character name of the
programming language used in this class (all lower case)
followed by 123. -->
</p>
</body>
</html>