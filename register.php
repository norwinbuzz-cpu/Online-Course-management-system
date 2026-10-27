<?php
require "config.php";
$pageTitle = "Student Registration";
$errors = [];
$name = $email = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    if (mb_strlen($name) < 2) $errors[] = "Name must contain at least 2 characters.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if (!$errors) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);
        if ($check->fetch()) $errors[] = "An account with this email already exists.";
        else {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (?,?,?,'student')");
            $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
            $_SESSION["flash"] = "Registration successful. Please log in.";
            header("Location: login.php"); exit;
        }
    }
}
require "includes/header.php";
?>
<div class="panel form-panel">
<h1>Create Student Account</h1><p class="muted">Register to browse and enroll in courses.</p>
<?php if ($errors): ?><div class="error"><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post">
  <div class="field"><label for="name">Full name</label><input id="name" name="name" required maxlength="100" value="<?= e($name) ?>"></div>
  <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required maxlength="150" value="<?= e($email) ?>"></div>
  <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required minlength="8"><small class="muted">Use at least 8 characters.</small></div>
  <button type="submit">Register</button>
</form><p class="muted">Already registered? <a href="login.php">Log in</a>.</p>
</div>
<?php require "includes/footer.php"; ?>