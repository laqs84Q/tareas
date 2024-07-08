<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $sql = "INSERT INTO tasks (title, description, due_date) VALUES ('$title', '$description', '$due_date')";

    if ($conn->query($sql) === TRUE) {
        echo "Nueva tarea creada exitosamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Tarea</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Añadir Nueva Tarea</h1>
        <div class="form-container">
            <form action="add_task.php" method="POST">
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" required>
                <label for="description">Descripción:</label>
                <textarea id="description" name="description" required></textarea>
                <label for="due_date">Fecha de Vencimiento:</label>
                <input type="date" id="due_date" name="due_date" required>
                <button type="submit">Añadir Tarea</button>
            </form>
        </div>
        <a class="return-link" href="index.php">Volver</a>
    </div>
</body>
</html>
