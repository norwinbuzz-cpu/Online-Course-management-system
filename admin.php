<?php
require "config.php";
if (empty($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    $_SESSION["flash"] = "Administrator login is required.";
    header("Location: login.php"); exit;
}
$pageTitle = "Admin Dashboard";
$errors = [];
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add_course") {
    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $duration = filter_input(INPUT_POST, "duration_hours", FILTER_VALIDATE_INT);
    $priceRaw = $_POST["price"] ?? "";
    if (mb_strlen($title) < 3) $errors[] = "Course title must contain at least 3 characters.";
    if ($category === "") $errors[] = "Category is required.";
    if (mb_strlen($description) < 10) $errors[] = "Description must contain at least 10 characters.";
    if (!$duration || $duration < 1 || $duration > 10000) $errors[] = "Duration must be a positive number of hours.";
    if (!is_numeric($priceRaw) || (float)$priceRaw < 0) $errors[] = "Price must be zero or a positive amount.";
    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO courses (title,category,description,duration_hours,price,instructor_id) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$title,$category,$description,$duration,(float)$priceRaw,$_SESSION["user_id"]]);
        $_SESSION["flash"] = "Course added successfully.";
        header("Location: admin.php"); exit;
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "delete_course") {
    $courseId = filter_input(INPUT_POST, "course_id", FILTER_VALIDATE_INT);
    if ($courseId) {
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id=?");
        $stmt->execute([$courseId]);
        $_SESSION["flash"] = "Course and related enrollments deleted.";
    }
    header("Location: admin.php"); exit;
}
$courseCount = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$studentCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$enrollmentCount = (int)$pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$courses = $pdo->query("SELECT c.*, u.name AS instructor FROM courses c LEFT JOIN users u ON c.instructor_id=u.id ORDER BY c.created_at DESC")->fetchAll();
$students = $pdo->query("SELECT id,name,email,created_at FROM users WHERE role='student' ORDER BY created_at DESC")->fetchAll();
require "includes/header.php";
?>
<div class="section-head"><div><h1>Admin Dashboard</h1><p class="muted">Manage course catalogue and review student accounts.</p></div><span class="tag">Administrator</span></div>
<div class="stats"><div class="stat"><strong><?= $courseCount ?></strong><span>Total courses</span></div><div class="stat"><strong><?= $studentCount ?></strong><span>Registered students</span></div><div class="stat"><strong><?= $enrollmentCount ?></strong><span>Total enrollments</span></div></div>
<div class="panel">
<h2>Add a Course</h2>
<?php if ($errors): ?><div class="error"><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="grid">
<input type="hidden" name="action" value="add_course">
<div class="field"><label for="title">Course title</label><input id="title" name="title" required maxlength="150" value="<?= e($_POST["title"] ?? "") ?>"></div>
<div class="field"><label for="category">Category</label><input id="category" name="category" required maxlength="80" value="<?= e($_POST["category"] ?? "") ?>"></div>
<div class="field"><label for="duration_hours">Duration (hours)</label><input id="duration_hours" name="duration_hours" type="number" min="1" max="10000" required value="<?= e($_POST["duration_hours"] ?? "10") ?>"></div>
<div class="field"><label for="price">Price (₹)</label><input id="price" name="price" type="number" min="0" step="0.01" required value="<?= e($_POST["price"] ?? "0") ?>"></div>
<div class="field" style="grid-column:1/-1"><label for="description">Description</label><textarea id="description" name="description" required minlength="10"><?= e($_POST["description"] ?? "") ?></textarea></div>
<div><button type="submit">Add course</button></div>
</form>
</div>
<h2>Course Catalogue</h2>
<div class="table-wrap"><table><thead><tr><th>Course</th><th>Category</th><th>Duration</th><th>Price</th><th>Action</th></tr></thead><tbody>
<?php foreach($courses as $course): ?><tr><td><?= e($course["title"]) ?></td><td><?= e($course["category"]) ?></td><td><?= (int)$course["duration_hours"] ?> hrs</td><td><?= $course["price"] > 0 ? "₹".number_format((float)$course["price"],2) : "Free" ?></td><td><form class="inline-form" method="post" onsubmit="return confirm('Delete this course and its enrollments?')"><input type="hidden" name="action" value="delete_course"><input type="hidden" name="course_id" value="<?= (int)$course["id"] ?>"><button class="btn danger" type="submit">Delete</button></form></td></tr><?php endforeach; ?>
<?php if (!$courses): ?><tr><td colspan="5">No courses available.</td></tr><?php endif; ?>
</tbody></table></div>
<h2>Registered Students</h2>
<div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Registered</th></tr></thead><tbody>
<?php foreach($students as $student): ?><tr><td><?= e($student["name"]) ?></td><td><?= e($student["email"]) ?></td><td><?= e(date("d M Y", strtotime($student["created_at"]))) ?></td></tr><?php endforeach; ?>
<?php if (!$students): ?><tr><td colspan="3">No students registered yet.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require "includes/footer.php"; ?>