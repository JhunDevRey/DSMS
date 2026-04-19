<?php
header('Content-Type: application/json');
include "connection.php";

$id = $_POST['id'];
$username = $_POST['username'];
$password = $_POST['password'];
$access = $_POST['access_level'];

$query = "UPDATE users 
          SET username='$username', password='$password', access_level='$access' 
          WHERE id='$id'";

if (mysqli_query($conn, $query)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>