<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$name = $_POST['name'];
$roll = $_POST['roll'];
$marks = $_POST['marks'];

$sql = "INSERT INTO students (name, roll, marks) VALUES ('$name', '$roll', '$marks')";

if ($conn->query($sql) === TRUE) {
    echo "Student Added Successfully";
} else {
    echo "Error";
}

$conn->close();
?>
