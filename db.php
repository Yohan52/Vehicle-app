<?php
$host = 'localhost';
$db = 'forum';
$user = 'root';
$pass = '';


$conn = new mysqli($host, $user, $pass, $db);

if($conn ->connect_error){
    die("conection failed: ". $conn ->connect_error);
}
