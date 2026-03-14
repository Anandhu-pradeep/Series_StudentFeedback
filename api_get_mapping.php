<?php
header('Content-Type: application/json');
include 'db.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : 1;
// Ensure year is at least 1
if ($year < 1) $year = 1;

$subjects = [
    'Mathematics',
    'Physics',
    'Computer Science',
    'Data Structures',
    'Software Engineering',
    'Database Management'
];

$stmt = $conn->prepare("SELECT * FROM teachers ORDER BY id ASC");
$stmt->execute();
$result = $stmt->get_result();

$mapping = [];
$t_index = 0;
while ($row = $result->fetch_assoc()) {
    $subject_index = ($t_index + ($year - 1)) % 6;
    if ($subject_index < 0) $subject_index += 6;
    
    $mapping[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'subject' => $subjects[$subject_index]
    ];
    $t_index++;
}

echo json_encode(['success' => true, 'teachers' => $mapping]);
$conn->close();
?>
