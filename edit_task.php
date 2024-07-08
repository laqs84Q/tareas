<?php
include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM tasks WHERE id=$id";
$result = $conn->query($sql);
$task = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $sql = "UPDATE tasks SET title='$title', description='$description', due_date='$due_date' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Tarea actualizada exitosamente";
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
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Tarea</h1>
        <div class="form-container">
            <form action="edit_task.php?id=<?php echo $id; ?>" method="POST">
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" value="<?php echo $task['title']; ?>" required>
                <label for="description">Descripción:</label>
                <textarea id="description" name="description" required><?php echo $task['description']; ?></textarea>
                <label for="due_date">Fecha de Vencimiento:</label>
                <input type="date" id="due_date" name="due_date" value="<?php echo $task['due_date']; ?>" required>
                <button type="submit">Actualizar Tarea</button>
            </form>
        </div>
        <a class="return-link" href="index.php">Volver</a>
    </div>
</body>
</html>
