<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$firstname = $_POST['firstname'];
$lastname =$_POST['lastname'];
$username = $_POST['username'];
$email = $_POST['email'];
$interests = isset($_POST['interests']) ? implode(",", $_POST['interests']) : '';

$query = "UPDATE users SET first_name = ?, last_name = ?, username = ? , email = ?, interests = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssssi",$firstname,$lastname,$username,$email,$interests,$user_id);
$stmt->execute();
$_SESSION['username'] = $username;

header("Location: dashboard.php?update=success");
exit;
?>