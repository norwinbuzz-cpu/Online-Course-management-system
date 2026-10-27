<?php
require "config.php";
if (empty($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "student") {
    $_SESSION["flash"] = "Please log in as a student to enroll.";
    header("Location: login.php"); exit;
}
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit; }
$courseId = filter_input(INPUT_POST, "course_id", FILTER_VALIDATE_INT);
if (!$courseId) {
    $_SESSION["flash"] = "Invalid course selected.";
    header("Location: index.php"); exit;
}
$check = $pdo->prepare("SELECT id FROM courses WHERE id=?");
$check->execute([$courseId]);
if (!$check->fetch()) {
    $_SESSION["flash"] = "Course not found.";
} else {
    $stmt = $pdo->prepare("INSERT IGNORE INTO enrollments (student_id,course_id) VALUES (?,?)");
    $stmt->execute([$_SESSION["user_id"], $courseId]);
    $_SESSION["flash"] = $stmt->rowCount() ? "You have enrolled successfully!" : "You are already enrolled in this course.";
}
header("Location: dashboard.php"); exit;
?>