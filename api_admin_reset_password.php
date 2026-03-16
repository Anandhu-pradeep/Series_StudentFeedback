<?php
header('Content-Type: application/json');
include 'db.php';

$id = $_POST['id'] ?? '';
$type = $_POST['type'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';

if (!$id || !$type || !$newPassword) {
    echo json_encode(['success' => false, 'message' => 'Required parameters missing.']);
    exit;
}

try {
    $table = ($type === 'student') ? 'students' : 'teachers';
    
    $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password reset successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update password.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
