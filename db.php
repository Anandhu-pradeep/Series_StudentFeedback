<?php
/*$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'feedback_system';*/

$host = 'sql313.infinityfree.com';
$user = 'if0_41386912';
$pass = '2FwH6fn9YWXOby';
$dbname = 'if0_41386912_feedback_system'; 

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
    }
} catch (Exception $e) {
    die(json_encode(["success" => false, "message" => "Database connection error. Admin: check InfinityFree DB settings."]));
}
?>
