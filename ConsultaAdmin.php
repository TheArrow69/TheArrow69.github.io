<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "campamentos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtiene los valores del formulario
$dni = $_POST['dni'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];

// Inicializa la variable para el mensaje de error
$error_message = "";

// Verificar que el campo nombre y apellidos estén llenos si uno de ellos está lleno
if (!empty($nombre) && empty($apellidos)) {
    $error_message = "Debes rellenar el campo de apellidos si has rellenado el nombre.";
} elseif (!empty($apellidos) && empty($nombre)) {
    $error_message = "Debes rellenar el campo de nombre si has rellenado el apellido.";
}

// Construcción de la consulta dinámica
$sql = "SELECT alumnos.Nombre, alumnos.Apellidos, alumnos.DNI, alumnos.Fecha_Nacimiento, 
               alumnos.Campamento, alumnos.Comentario, alumnos.Fecha_Inicio AS Fecha_Inicio,
               contacto.email, contacto.Contacto
        FROM alumnos 
        LEFT JOIN contacto ON alumnos.DNI = contacto.DNI
        WHERE 1=1";

// Inicializa los arrays para los parámetros
$params = array();
$types = "";

// Agrega condiciones en base a los campos rellenados
if (!empty($dni)) {
    $sql .= " AND alumnos.DNI = ?";
    $params[] = $dni;
    $types .= "s";
} elseif (empty($error_message) && !empty($nombre) && !empty($apellidos)) {
    // Solo realizar la búsqueda si ambos campos están llenos
    $sql .= " AND alumnos.Nombre LIKE ? AND alumnos.Apellidos LIKE ?";
    $params[] = "%" . $nombre . "%";
    $params[] = "%" . $apellidos . "%";
    $types .= "ss";
}

// Prepara y ejecuta la consulta
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de la Búsqueda - Admin</title>
    <link rel="stylesheet" href="css/registro-styles.css">
</head>
<body class="bg-camp min-h-screen flex items-center justify-center">
    <div class="container flex flex-col items-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg">
            <div class="card-header">
                <h1>Resultado de la Búsqueda</h1>
            </div>
            <div class="card-body">
                <?php
                // Mostrar mensaje de error si existe
                if (!empty($error_message)) {
                    echo "<p style='color: red;'>$error_message</p>";
                } elseif ($result->num_rows > 0) {
                    // Muestra los resultados
                    while ($row = $result->fetch_assoc()) {
                        echo "<p><strong>DNI:</strong> " . $row["DNI"] . "</p>";
                        echo "<p><strong>Nombre:</strong> " . $row["Nombre"] . "</p>";
                        echo "<p><strong>Apellidos:</strong> " . $row["Apellidos"] . "</p>";
                        echo "<p><strong>Fecha de Nacimiento:</strong> " . $row["Fecha_Nacimiento"] . "</p>";
                        echo "<p><strong>Campamento:</strong> " . $row["Campamento"] . "</p>";
                        echo "<p><strong>Fecha de Inicio del Campamento:</strong> " . $row["Fecha_Inicio"] . "</p>";
                        echo "<p><strong>Comentarios:</strong> " . $row["Comentario"] . "</p>";
                        echo "<p><strong>Email:</strong> " . $row["email"] . "</p>";
                        echo "<p><strong>Contacto Telefónico:</strong> " . $row["Contacto"] . "</p>";
                        echo "<hr>";
                    }
                } else {
                    echo "<p>No se encontraron resultados para los criterios ingresados.</p>";
                }
                ?>
                <div class="form-group">
                    <a href="admin-dashboard.html">
                        <button class="btn">Volver a la Búsqueda</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Cierra la conexión
$stmt->close();
$conn->close();
?>


