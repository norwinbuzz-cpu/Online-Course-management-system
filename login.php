<?php
require "config.php";
$pageTitle = "Login";
$error = "";
$email = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $stmt = $pdo->prepare("SELECT id,name,email,password_hash,role FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        header("Location: " . ($user["role"] === "admin" ? "admin.php" : "dashboard.php"));
        exit;
    }
    $error = "Invalid email or password.";
}
require "includes/header.php";
?>
<div class="panel form-panel">
<h1>Welcome back</h1><p class="muted">Log in to continue learning.</p>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
  <div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" required value="<?= e($email) ?>"></div>
  <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" required></div>
  <button type="submit">Login</button>
</form>
<p class="muted">New student? <a href="register.php">Create an account</a>.</p>
<p class="small muted">Demo admin after importing SQL: admin@coursehub.test / Admin@123</p>
</div>
<?php require "includes/footer.php"; ?>