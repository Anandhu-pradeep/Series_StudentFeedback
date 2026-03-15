<?php
// Prevent any PHP errors or warnings from being printed to the output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    if (!file_exists('db.php')) {
        throw new Exception("Database configuration file missing.");
    }
    
    include 'db.php';
    
    // Check if $conn was created successfully in db.php
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed. Please check db.php settings.");
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    $reg_number = trim($data['regNumber'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($reg_number) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please enter both Registration ID and Password.']);
        exit;
    }

    // Hardcoded Admin Check (Double check case sensitivity)
    if (strtolower($reg_number) === 'admin' && $password === 'admin123') {
        $admin_user = [
            'id' => 0,
            'fullName' => 'System Administrator',
            'email' => 'admin@edufeedback.com',
            'regNumber' => 'admin',
            'year' => 'N/A',
            'password' => '********',
            'role' => 'Admin'
        ];
        echo json_encode(['success' => true, 'user' => $admin_user]);
        exit;
    }

    $user = null;

    // 1. Check Students Table
    $stmt = $conn->prepare("SELECT * FROM students WHERE reg_number = ? AND password = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $reg_number, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }
        $stmt->close();
    }

    // 2. Check Teachers Table (only if not found in students)
    if (!$user) {
        $stmt = $conn->prepare("SELECT * FROM teachers WHERE reg_number = ? AND password = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $reg_number, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $user['full_name'] = $user['name']; 
                $user['str_year'] = 'N/A';
            }
            $stmt->close();
        }
    }

    // Final Response
    if ($user) {
        $frontend_user = [
            'id' => $user['id'],
            'fullName' => $user['full_name'],
            'email' => $user['email'] ?? '',
            'regNumber' => $user['reg_number'],
            'year' => $user['str_year'] ?? '1',
            'role' => $user['role']
        ];
        echo json_encode(['success' => true, 'user' => $frontend_user]);
    } else {
        // This is exactly what shows if password/ID is wrong
        echo json_encode(['success' => false, 'message' => 'Invalid Registration ID or password!']);
    }

} catch (Exception $e) {
    // If anything fails (db, table missing, etc), return a clean JSON error
    echo json_encode(['success' => false, 'message' => 'System encountered an error. Please try again later.']);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>
