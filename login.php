<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambia si es necesario
$username = "root"; // Cambia a tu usuario de MySQL
$password = ""; // Cambia a tu contraseña de MySQL
$database = "campamentos"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$email = $_POST['email'];
$contrasena = $_POST['password'];

// Verificar si es el usuario administrador
if ($email === "admin@gmail.com" && $contrasena === "Aa123456") {
    // Redirigir al panel de administrador
    header("Location: admin-dashboard.html");
    exit();
}

// Consulta para verificar las credenciales de otros usuarios
$sql = "SELECT DNI FROM sesiones WHERE email = ? AND contrasena = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $contrasena); // Protección contra inyección SQL
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows > 0) {
    // Si el usuario existe, obtener el DNI
    $row = $result->fetch_assoc();
    $dni = $row['DNI'];

    // Consultar la información relacionada con el DNI
    $sql = "SELECT alumnos.Nombre, alumnos.Apellidos, alumnos.DNI, alumnos.Fecha_Nacimiento, 
                   alumnos.Campamento, alumnos.Comentario, alumnos.Fecha_Inicio AS Fecha_Inicio,
                   contacto.email, contacto.Contacto
            FROM alumnos 
            LEFT JOIN contacto ON alumnos.DNI = contacto.DNI
            WHERE alumnos.DNI = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    // Comienza a mostrar los resultados
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Información del Usuario</title>
        <link rel="stylesheet" href="css/registro-styles.css">
    </head>
    <body class="bg-camp min-h-screen flex items-center justify-center">
        <div class="container flex flex-col items-center">
            <div class="max-w-md w-full bg-white rounded-lg shadow-lg">
                <div class="card-header">
                    <h1>Información del Usuario</h1>
                </div>
                <div class="card-body">
                    <?php
                    if ($result->num_rows > 0) {
                        // Muestra los resultados
                        while ($row = $result->fetch_assoc()) {
                            echo "<p><strong>DNI:</strong> " . htmlspecialchars($row["DNI"]) . "</p>";
                            echo "<p><strong>Nombre:</strong> " . htmlspecialchars($row["Nombre"]) . "</p>";
                            echo "<p><strong>Apellidos:</strong> " . htmlspecialchars($row["Apellidos"]) . "</p>";
                            echo "<p><strong>Fecha de Nacimiento:</strong> " . htmlspecialchars($row["Fecha_Nacimiento"]) . "</p>";
                            echo "<p><strong>Campamento:</strong> " . htmlspecialchars($row["Campamento"]) . "</p>";
                            echo "<p><strong>Fecha de Inicio del Campamento:</strong> " . htmlspecialchars($row["Fecha_Inicio"]) . "</p>";
                            echo "<p><strong>Comentarios:</strong> " . htmlspecialchars($row["Comentario"]) . "</p>";
                            echo "<p><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
                            echo "<p><strong>Contacto Telefónico:</strong> " . htmlspecialchars($row["Contacto"]) . "</p>";
                            echo "<hr>";
                        }
                    } else {
                        echo "<p>No se encontraron resultados para el DNI asociado.</p>";
                    }
                    ?>
                    <div class="form-group">
                        <a href="Campamento-login.html">
                            <button class="btn">Cerrar Sesión</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    // Mensaje si el inicio de sesión falla
    echo "<p style='color: red;'>Credenciales incorrectas. Inténtalo de nuevo.</p>";
    echo "<a href='Campamento-login.html'>Volver a intentar</a>";
}

// Cierra la conexión
$stmt->close();
$conn->close();
?>

