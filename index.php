<?php
require "config.php";
$pageTitle = "Explore Courses";
$stmt = $pdo->query("SELECT c.*, u.name AS instructor FROM courses c LEFT JOIN users u ON c.instructor_id=u.id ORDER BY c.created_at DESC");
$courses = $stmt->fetchAll();
require "includes/header.php";
?>
<section class="hero">
  <h1>Learn something new today.</h1>
  <p>Explore practical online courses, build your skills, and keep your learning journey moving forward with CourseHub.</p>
  <a class="btn" href="register.php">Create a student account</a>
  <a class="btn secondary" href="#courses">Browse courses</a>
</section>
<div class="section-head"><div><h2 id="courses">Available Courses</h2><p class="muted">Choose a course and enroll to get started.</p></div></div>
<?php if (!$courses): ?>
  <div class="empty">No courses have been added yet. Log in as an administrator to add courses.</div>
<?php else: ?>
<div class="grid">
<?php foreach ($courses as $course): ?>
  <article class="card">
    <span class="tag"><?= e($course["category"]) ?></span>
    <h3><?= e($course["title"]) ?></h3>
    <p><?= e($course["description"]) ?></p>
    <p class="muted small">Instructor: <?= e($course["instructor"] ?: "CourseHub Team") ?> · <?= (int)$course["duration_hours"] ?> hours</p>
    <div class="price"><?= $course["price"] > 0 ? "₹".number_format((float)$course["price"],2) : "Free" ?></div>
    <?php if (!empty($_SESSION["user_id"]) && ($_SESSION["role"] ?? "") === "student"): ?>
      <form method="post" action="enroll.php">
        <input type="hidden" name="course_id" value="<?= (int)$course["id"] ?>">
        <button type="submit">Enroll now</button>
      </form>
    <?php elseif (empty($_SESSION["user_id"])): ?>
      <a class="btn" href="login.php">Login to enroll</a>
    <?php else: ?>
      <a class="btn secondary" href="admin.php">Manage course</a>
    <?php endif; ?>
  </article>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php require "includes/footer.php"; ?>