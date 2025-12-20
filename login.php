<?php
require_once 'bootstrap.php';

$salt = 'XyZzy12*_';

if (isset($_POST['cancel'])) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (strlen($_POST['email']) === 0 || strlen($_POST['pass']) === 0) {
        redirectWithMessage('login.php', 'error', 'Email and password are required');
    }

    $check = hash('md5', $salt . $_POST['pass']);
    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute([
        ':em' => $_POST['email'],
        ':pw' => $check
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
        redirectWithMessage('index.php', 'success', 'Logged in.');
    }

    redirectWithMessage('login.php', 'error', 'Incorrect password');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mehdi - Login</title>
    <script>
        function doValidate() {
            console.log('Validating...');
            try {
                var addr = document.getElementById('email').value;
                var pw = document.getElementById('id_1723').value;
                if (addr === null || addr === '' || pw === null || pw === '') {
                    alert('Both fields must be filled out');
                    return false;
                }
                if (addr.indexOf('@') === -1) {
                    alert('Email address must contain @');
                    return false;
                }
                return true;
            } catch (e) {
                return false;
            }
        }
    </script>
</head>
<body>
    <h1>Please Log In</h1>
    <?php echo flashMessages(); ?>
    <form method="POST">
        <p>Email
            <input type="text" name="email" id="email"></p>
        <p>Password
            <input type="password" name="pass" id="id_1723"></p>
        <input type="submit" value="Log In" onclick="return doValidate();">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</body>
</html>
