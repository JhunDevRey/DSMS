<?php
session_start();
$_SESSION = [];
session_destroy();

setcookie(session_name(), '', time() - 3600, '/');

header('Content-Type: application/json');
echo json_encode(["success" => true]);
exit;