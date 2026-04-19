<?php
session_start();
include "connection.php";

header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['access_level'] !== 'admin') {
    echo json_encode(["success" => false, "error" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(["success" => false, "error" => "Invalid ID"]);
    exit;
}

$student_id = intval($data['id']);

$query = "DELETE FROM employee WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>