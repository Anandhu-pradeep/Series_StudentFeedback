<?php
header('Content-Type: application/json');
include 'db.php';

$id = $_POST['id'] ?? 0;
$type = $_POST['type'] ?? '';

if (!$id || !$type) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if ($type === 'student') {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
} else if ($type === 'teacher') {
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit;
}

$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
