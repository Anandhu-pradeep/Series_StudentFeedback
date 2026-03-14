<?php
header('Content-Type: application/json');
include 'db.php';

$student_id = isset($_GET['studentId']) ? intval($_GET['studentId']) : 0;
$teacher_id = isset($_GET['teacherId']) ? intval($_GET['teacherId']) : 0;

if ($student_id) {
    $stmt = $conn->prepare("
        SELECT f.*, t.name as teacher_name 
        FROM feedbacks f 
        JOIN teachers t ON f.teacher_id = t.id 
        WHERE f.student_id = ? 
        ORDER BY f.date DESC
    ");
    $stmt->bind_param("i", $student_id);
} else if ($teacher_id) {
    $stmt = $conn->prepare("
        SELECT f.*, t.name as teacher_name, s.full_name as student_name
        FROM feedbacks f 
        JOIN teachers t ON f.teacher_id = t.id 
        LEFT JOIN students s ON f.student_id = s.id
        WHERE f.teacher_id = ? 
        ORDER BY f.date DESC
    ");
    $stmt->bind_param("i", $teacher_id);
} else {
    $stmt = $conn->prepare("
        SELECT f.*, t.name as teacher_name, s.full_name as student_name
        FROM feedbacks f 
        JOIN teachers t ON f.teacher_id = t.id 
        LEFT JOIN students s ON f.student_id = s.id
        ORDER BY f.date DESC
    ");
}

$stmt->execute();
$result = $stmt->get_result();

$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $ratings = [
        $row['q1'], $row['q2'], $row['q3'], $row['q4'], 
        $row['q5'], $row['q6'], $row['q7'], $row['q8'], $row['q9']
    ];
    $feedbacks[] = [
        'id' => $row['id'],
        'studentId' => $row['student_id'],
        'teacherId' => $row['teacher_id'],
        'teacherName' => $row['teacher_name'],
        'studentName' => $row['student_name'] ?? 'Anonymous',
        'subject' => $row['subject'],
        'ratings' => $ratings,
        'suggestion' => $row['suggestion'],
        'date' => $row['date']
    ];
}

echo json_encode(['success' => true, 'feedbacks' => $feedbacks]);
$conn->close();
?>
