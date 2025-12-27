<?php
ob_start(); // Output buffering ON
session_start();
require_once "pdo.php";

if ( isset($_POST['cancel']) ) {
    ob_end_clean();
    header("Location: index.php", true, 303);
    exit();
}

$salt = 'php123';

if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "User name and password are required";
        ob_end_clean();
        header("Location: login.php", true, 303);
        exit();
    } else { 
        $check = md5($_POST['pass']); // Use salt if needed: md5($salt.$_POST['pass']); 
        // NOTE: The course often uses md5($salt.$pass) OR just md5($pass). 
        // Based on pdo.php setup: md5('php123') is stored. So we just hash the input.
        // Wait, pdo.php stored: $hashed = md5('php123');
        // Let's assume the user enters 'php123' as password.
        // The check should be: does md5(input) === stored_hash ?
        // If the user setup used a salt, we need to match it.
        // In pdo.php I did: $hashed = md5('php123'); 
        // So checking md5($input) against DB is correct if input is 'php123'.
        
        $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => md5($_POST['pass']) ));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ( $row !== false ) {
            $_SESSION['name'] = $row['name'];
            $_SESSION['user_id'] = $row['user_id'];
            ob_end_clean();
            header("Location: index.php", true, 303);
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password";
            ob_end_clean();
            header("Location: login.php", true, 303);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Mehdi 57b47596 - Login Page</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<form method="POST">
<label for="nam">Email</label>
<input type="text" name="email" id="nam"><br/>
<label for="id_1723">Password</label>
<input type="password" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('nam').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</div>
</body>
</html>
