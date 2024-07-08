<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $new_date = $_POST['new_date'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("UPDATE tasks SET due_date = ? WHERE id = ?");
    $stmt->bind_param("si", $new_date, $id);
    
    if ($stmt->execute()) {
        echo "Fecha de la tarea actualizada exitosamente.";
    } else {
        echo "Error actualizando la fecha de la tarea: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
