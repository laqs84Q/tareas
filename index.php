<?php
include 'db.php';

$sql = "SELECT * FROM tasks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de Tareas</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.css">
    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Gestor de Tareas</h1>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button" onclick="openTab(event, 'tasks')">Tareas</button>
            <button class="tab-button" onclick="openTab(event, 'calendar')">Calendario</button>
        </div>

        <!-- Tabs Content -->
        <div id="tasks" class="tab-content">
            <a class="button" href="add_task.php">Añadir Nueva Tarea</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Fecha de Vencimiento</th>
                        <th>Completada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo $row['is_completed'] ? 'Sí' : 'No'; ?></td>
                        <td>
                            <a class="button" href="edit_task.php?id=<?php echo $row['id']; ?>">Editar</a>
                            <a class="button" href="delete_task.php?id=<?php echo $row['id']; ?>">Eliminar</a>
                            <?php if (!$row['is_completed']) { ?>
                            <a class="button" href="complete_task.php?id=<?php echo $row['id']; ?>">Completar</a>
                            <?php }?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div id="calendar" class="tab-content" style="display: none;">
            <div id="calendar-container"></div>
        </div>
    </div>

    <!-- Modal HTML -->
<div id="task-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Detalles de la Tarea</h2>
        <p id="modal-title"></p>
        <p id="modal-description"></p>
        <p id="modal-due-date"></p>
    </div>
</div>


    <script>
        // Function to initialize the calendar
        function initializeCalendar() {
    var calendarEl = document.getElementById('calendar-container');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        editable: true,
        dayMaxEvents: true,
        events: [
            <?php
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                $title = $row['title'];
                $description = $row['description'];
                $due_date = $row['due_date'];
                $id = $row['id'];
                // Ensure the date format is compatible with FullCalendar
                $start = date('Y-m-d', strtotime($due_date));
                echo "{ title: '$title', start: '$start', description: '$description', id: '$id' },";
            }
            ?>
        ],
        eventClick: function(info) {
            // Populate modal with event details
            document.getElementById('modal-title').innerText = info.event.title;
            document.getElementById('modal-description').innerText = info.event.extendedProps.description;
            document.getElementById('modal-due-date').innerText = `Fecha de Vencimiento: ${info.event.start.toISOString().split('T')[0]}`;
            
            // Show the modal
            var modal = document.getElementById('task-modal');
            modal.style.display = 'block';
        },
        eventDrop: function(info) {
            var newDate = info.event.start.toISOString().split('T')[0];
            var taskId = info.event.id;
            // Send AJAX request to update the date
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_task_date.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                            toastr.success('Fecha de la tarea actualizada exitosamente');
                        } else if (xhr.readyState === 4) {
                            toastr.error('Error actualizando la fecha de la tarea');
                        }
            };
            xhr.send("id=" + taskId + "&new_date=" + newDate);
        }
    });
    calendar.render();
}


// Function to switch tabs
function openTab(evt, tabName) {
    var i, tabcontent, tabbuttons;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tabbuttons = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabbuttons.length; i++) {
        tabbuttons[i].className = tabbuttons[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";

    // Initialize calendar only if the calendar tab is opened
    if (tabName === 'calendar' && !document.getElementById('calendar-container').hasChildNodes()) {
        initializeCalendar();
    }
}

// Function to close the modal
function closeModal() {
    var modal = document.getElementById('task-modal');
    modal.style.display = 'none';
}

// Initialize the first tab as active
document.querySelector('.tab-button').click();

// Add event listener to close button
document.querySelector('.close').addEventListener('click', closeModal);

// Add event listener to close modal when clicking outside of it
window.onclick = function(event) {
    var modal = document.getElementById('task-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

    </script>
</body>
</html>

<?php
$conn->close();
?>
