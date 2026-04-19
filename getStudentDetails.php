<?php
include "connection.php";

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM employee WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "No data found"]);
}
?>