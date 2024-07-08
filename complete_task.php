<?php
include 'db.php';

$id = $_GET['id'];
$sql = "UPDATE tasks SET is_completed=TRUE WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Tarea marcada como completada";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header('Location: index.php');
