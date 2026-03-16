<?php
header('Content-Type: application/json');
include 'db.php';

$name = $_POST['name'] ?? '';
$reg = $_POST['reg'] ?? '';
$pass = $_POST['pass'] ?? '';

if (!$name || !$reg || !$pass) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

try {
    // Check if Teacher Reg ID already exists
    $stmt = $conn->prepare("SELECT id FROM teachers WHERE reg_number = ?");
    $stmt->bind_param("s", $reg);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Teacher with this Registration ID already exists.']);
        exit;
    }

    // Insert new teacher
    $stmt = $conn->prepare("INSERT INTO teachers (name, reg_number, password, role) VALUES (?, ?, ?, 'Teacher')");
    $stmt->bind_param("sss", $name, $reg, $pass);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Teacher account created.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create teacher account.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>
