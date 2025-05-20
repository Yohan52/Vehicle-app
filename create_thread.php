<?php

session_start();
require 'db.php';
header('Content-Type: application/json');



if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "You must be logged in to post a thread."]);
    exit;

}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $tags = trim($_POST['tags']);
    $user_id = $_SESSION['user_id'];
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
   
    $check = $conn->prepare("SELECT 1 FROM categories WHERE category_id = ?");
    $check->bind_param("i", $category_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Selected category does not exit."]);
        exit;
    }

    $sql = "INSERT INTO threads (user_id, title , content, tags, category_id, created_at) VALUES (?,?,?,?,?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi",$user_id, $title, $content, $tags, $category_id);

    

    if ($stmt->execute()) {
        $update = $conn->prepare("UPDATE categories SET threads_count = threads_count+1 WHERE category_id = ?");
        $update->bind_param("i", $category_id);
        $update->execute();
        $update->close();

        $latest = $conn->prepare("UPDATE categories SET latest_thread_title = ? WHERE category_id = ?");
        $latest->bind_param("si", $title, $category_id);
        $latest->execute();
        $latest->close();

        echo json_encode(["success" => true]);
        
    }else {
        echo json_encode(["success" => false, "error" => "Failed to insert thread."]);
    }
    $stmt->close();
    exit;
}
