<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS feedback_system";
if ($conn->query($sql) === TRUE) {
    echo "Database feedback_system ensured.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

$conn->select_db("feedback_system");

// Create Students table
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    reg_number VARCHAR(100) NOT NULL UNIQUE,
    str_year VARCHAR(50) NOT NULL DEFAULT '1',
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Student',
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table students ready.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Drop existing to ensure schema update
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("DROP TABLE IF EXISTS feedbacks");
$conn->query("DROP TABLE IF EXISTS teachers");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Create Teachers table
$sql = "CREATE TABLE IF NOT EXISTS teachers (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    reg_number VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Teacher'
)";
if ($conn->query($sql) === TRUE) {
    echo "Table teachers ready.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create Feedback table
$sql = "CREATE TABLE IF NOT EXISTS feedbacks (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT(6) UNSIGNED,
    teacher_id INT(6) UNSIGNED,
    subject VARCHAR(255) NOT NULL,
    q1 INT(2), q2 INT(2), q3 INT(2), q4 INT(2), q5 INT(2), q6 INT(2), q7 INT(2), q8 INT(2), q9 INT(2),
    suggestion TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY(teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
)";
$conn->query($sql);

// Insert initial teachers
$teachers_data = [
    ['name' => 'Ann mary', 'reg' => 'MCA 10001'],
    ['name' => 'Anit james', 'reg' => 'MCA 10002'],
    ['name' => 'Nimmy Francis', 'reg' => 'MCA 10003'],
    ['name' => 'Ajith G S', 'reg' => 'MCA 10004'],
    ['name' => 'Amal K Jose', 'reg' => 'MCA 10005'],
    ['name' => 'Lisha Varghese', 'reg' => 'MCA 10006']
];

foreach ($teachers_data as $t) {
    $firstName = explode(' ', $t['name'])[0];
    $password = $firstName . '123';
    
    $stmt = $conn->prepare("INSERT INTO teachers (name, reg_number, password, role) VALUES (?, ?, ?, 'Teacher') ON DUPLICATE KEY UPDATE reg_number=VALUES(reg_number), password=VALUES(password)");
    $stmt->bind_param("sss", $t['name'], $t['reg'], $password);
    $stmt->execute();
}
echo "Teachers seeded with login credentials.<br>";
echo "Setup complete! You can now use the application.";
$conn->close();
?>
