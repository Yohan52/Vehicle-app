<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['loginEmail']) && isset($_POST['password'])) {
        $email = trim($_POST['loginEmail']);
        $password = $_POST['password'];

        
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo 'success';
            } else {
                echo 'Incorrect password';
            }
        } else {
            echo 'Email not found';
        }

        $stmt->close();
        $conn->close();
    }else{
        echo 'missing email or password';
    }
}

