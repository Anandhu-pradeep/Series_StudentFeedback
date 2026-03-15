<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$full_name = $data['fullName'] ?? '';
$email = $data['email'] ?? '';
$reg_number = $data['regNumber'] ?? '';
$year = $data['year'] ?? '1';
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'Student';

if (!$reg_number || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing fields']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM students WHERE reg_number = ?");
$stmt->bind_param("s", $reg_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Registration ID already exists']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO students (full_name, email, reg_number, str_year, password, role) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $full_name, $email, $reg_number, $year, $password, $role);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
$conn->close();
?>
