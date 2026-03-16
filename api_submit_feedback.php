<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$student_id = $data['studentId'] ?? 0;
$teacher_id = $data['teacherId'] ?? 0;
$subject = $data['subject'] ?? '';
$ratings = $data['ratings'] ?? [];
$suggestion = $data['suggestion'] ?? '';

if (!$student_id || !$teacher_id || !$subject || count($ratings) < 9) {
    echo json_encode(['success' => false, 'message' => 'Missing fields for feedback...']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO feedbacks (student_id, teacher_id, subject, q1, q2, q3, q4, q5, q6, q7, q8, q9, suggestion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisiiiiiiiiis", $student_id, $teacher_id, $subject, $ratings[0], $ratings[1], $ratings[2], $ratings[3], $ratings[4], $ratings[5], $ratings[6], $ratings[7], $ratings[8], $suggestion);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
$conn->close();
?>
