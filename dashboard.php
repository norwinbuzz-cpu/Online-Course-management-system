<?php
require "config.php";
if (empty($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "student") {
    $_SESSION["flash"] = "Please log in as a student to view your dashboard.";
    header("Location: login.php"); exit;
}
$pageTitle = "Student Dashboard";
$stmt = $pdo->prepare("SELECT c.*, e.enrolled_at FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.student_id=? ORDER BY e.enrolled_at DESC");
$stmt->execute([$_SESSION["user_id"]]);
$courses = $stmt->fetchAll();
require "includes/header.php";
?>
<div class="section-head"><div><h1>Hello, <?= e($_SESSION["user_name"]) ?>!</h1><p class="muted">Here are the courses you have joined.</p></div><a class="btn" href="index.php">Explore courses</a></div>
<div class="stats"><div class="stat"><strong><?= count($courses) ?></strong><span>Courses enrolled</span></div><div class="stat"><strong>Learn</strong><span>At your own pace</span></div></div>
<h2>My Courses</h2>
<?php if (!$courses): ?><div class="empty">You have not enrolled in any courses yet. <a href="index.php">Browse courses</a>.</div>
<?php else: ?><div class="grid"><?php foreach($courses as $course): ?><article class="card"><span class="tag"><?= e($course["category"]) ?></span><h3><?= e($course["title"]) ?></h3><p><?= e($course["description"]) ?></p><p class="muted small">Enrolled on <?= e(date("d M Y", strtotime($course["enrolled_at"]))) ?></p><span class="tag">Enrollment confirmed</span></article><?php endforeach; ?></div><?php endif; ?>
<?php require "includes/footer.php"; ?>