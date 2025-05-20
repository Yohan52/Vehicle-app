<?php
header('Content-Type: application/json');
include 'db.php';

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$interests = $_POST['selectedInterests'];

$checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ?");
if (!$checkUsername) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}
$checkUsername->bind_param("s", $username);
$checkUsername->execute();
$checkUsername->store_result();


if ($checkUsername->num_rows > 0) {
    echo json_encode(['error' => 'Username already exists']);
    $checkUsername->close();
    $conn->close();
    exit;
}
$checkUsername->close();


$checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$checkEmail) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    echo json_encode(['error' => 'Email already exists']);
    $checkEmail->close();
    $conn->close();
    exit;
}
$checkEmail->close();

$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, interests) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss",$firstName,$lastName,$username, $email, $password, $interests);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
}else{
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close();
