<?php
// Login page
require_once 'pdo.php';
require_once 'util.php';

// Handle POST - login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['pass'] ?? '';

    if (strlen($email) === 0 || strlen($password) === 0) {
        setFlash('Email and password are required');
        header('Location: login.php');
        exit();
    }

    // MD5 hash the password (as per assignment spec)
    $passwordHash = md5($password);

    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :email AND password = :password');
    $stmt->execute([':email' => $email, ':password' => $passwordHash]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        setFlash('Welcome ' . $user['name']);
        header('Location: index.php');
        exit();
    } else {
        setFlash('Invalid email or password');
        header('Location: login.php');
        exit();
    }
}

echo getHeader('Login');
$flash = getFlash();
if ($flash) {
    echo '<p class="flash-error">' . htmlEscape($flash) . '</p>';
}
?>

<h1>Login</h1>
<form method="post">
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" class="form-control" />
    </div>
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="pass" id="password" class="form-control" />
    </div>
    <input type="submit" value="Log In" class="btn btn-primary" />
    <a href="index.php" class="btn btn-default">Cancel</a>
</form>

<p style="margin-top: 20px;">
    <strong>Test accounts:</strong><br>
    Email: csev@umich.edu, Password: php123<br>
    Email: umsi@umich.edu, Password: php123
</p>

<?php echo getFooter(); ?>