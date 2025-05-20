<?php
session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';



if ($_SERVER['REQUEST_METHOD'] ==='POST') {
    if(!isset($_SESSION['user_id'])) {
        echo json_encode(['status' => 'error','message'=>'You must logged in to post a reply']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $thread_id = isset($_POST['thread_id']) ? intval($_POST['thread_id']) : 0;
    $content = trim($_POST['content']);

    if ($thread_id <= 0) {
        $error="Invalid thread ID.";
        echo json_encode(['status' => 'error','message' => 'Invalid thread ID.']);
        exit;
    }

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO replies (thread_id, user_id, content, created_at) VALUES (?,?,?, NOW())");
        $stmt->bind_param("iis", $thread_id, $user_id, $content);

        if($stmt->execute()) {
            echo json_encode(['status' => 'success','message' => 'Reply posted successfully.']);
            exit;
        } else {
            echo json_encode(['status' => 'error','message' => 'Database error:' . $stmt->error]);
        }
    } else {
        echo json_encode(['status' => 'error','message' => 'Reply content cannot be empty.']);
    }
}
?>