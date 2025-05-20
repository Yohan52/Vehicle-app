<?php
header('Content-Type: application/json');
require_once 'db.php';

$sql = "SELECT * FROM categories ORDER BY name ASC";
$result = $conn->query($sql);

$categories = [];

if ($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

echo json_encode($categories);

$conn->close();