<?php
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$id = $data['id'] ?? 0;
$full_name = $data['fullName'] ?? '';
$email = $data['email'] ?? '';
$year = $data['year'] ?? '';
$password = $data['password'] ?? '';

if (!$id || !$full_name || !$email) {
    echo json_encode(['success' => false, 'message' => 'Missing basic fields']);
    exit;
}

if ($year) {
    $stmt = $conn->prepare("UPDATE students SET full_name=?, email=?, str_year=?, password=? WHERE id=?");
    $stmt->bind_param("ssssi", $full_name, $email, $year, $password, $id);
} else {
    $stmt = $conn->prepare("UPDATE students SET full_name=?, email=?, password=? WHERE id=?");
    $stmt->bind_param("sssi", $full_name, $email, $password, $id);
}

if ($stmt->execute()) {
    // Return updated user
    $stmt2 = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $res = $stmt2->get_result()->fetch_assoc();
    $frontend_user = [
        'id' => $res['id'],
        'fullName' => $res['full_name'],
        'email' => $res['email'],
        'regNumber' => $res['reg_number'],
        'year' => $res['str_year'],
        'password' => $res['password'],
        'role' => $res['role']
    ];
    echo json_encode(['success' => true, 'user' => $frontend_user]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
$conn->close();
?>
