<?php
header('Content-Type: application/json');
include "connection.php";

$id = intval($_GET['id']);

$query = "DELETE FROM users WHERE id = $id";

if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>