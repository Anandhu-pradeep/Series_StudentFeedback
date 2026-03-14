<?php
header('Content-Type: application/json');
include 'db.php';

// In a real app, you'd check session here
// session_start();
// if($_SESSION['role'] !== 'Admin') die();

$response = ['success' => true];

// Get Students
$students = [];
$res = $conn->query("SELECT * FROM students");
while($row = $res->fetch_assoc()) {
    unset($row['password']);
    $students[] = $row;
}
$response['students'] = $students;

// Get Teachers
$teachers = [];
$res = $conn->query("SELECT * FROM teachers");
while($row = $res->fetch_assoc()) {
    unset($row['password']);
    $teachers[] = $row;
}
$response['teachers'] = $teachers;

// Get Feedbacks with Student Info
$feedbacks = [];
$res = $conn->query("
    SELECT f.*, t.name as teacher_name, s.full_name as student_name, s.reg_number as student_reg
    FROM feedbacks f 
    JOIN teachers t ON f.teacher_id = t.id 
    JOIN students s ON f.student_id = s.id
    ORDER BY f.date DESC
");
while($row = $res->fetch_assoc()) {
    $feedbacks[] = $row;
}
$response['feedbacks'] = $feedbacks;

echo json_encode($response);
$conn->close();
?>
