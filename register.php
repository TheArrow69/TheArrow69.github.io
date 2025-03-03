<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambia si es necesario
$username = "root"; // Cambia a tu usuario de MySQL
$password = ""; // Cambia a tu contraseña de MySQL
$database = "campamentos"; // Nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $database);

// Verificación de conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Capturando datos del formulario
$apellidos = $_POST['Apellidos'];
$nombre = $_POST['Nombre'];
$correo_electronico = $_POST['email']; // No se usa en el insert actual, revisa si es necesario
$dni = $_POST['DNI'];
$contrasena = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar contraseña
$telefono_contacto = $_POST['tlf']; // No se usa en el insert actual, revisa si es necesario
$comentarios = $_POST['Comentarios']; // Asegúrate de que el campo en el formulario sea correcto
$fecha_nacimiento = $_POST['Nacimiento'];
$fecha_inicio = $_POST['FechaCampamento'];
$campamento = $_POST['Campamento'];

// Preparar y vincular las consultas
$stmt = $conn->prepare("INSERT INTO alumnos (dni, nombre, apellidos, fecha_nacimiento, campamento, fecha_inicio, comentario) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $dni, $nombre, $apellidos, $fecha_nacimiento, $campamento, $fecha_inicio, $comentarios);

$stmt2 = $conn->prepare("INSERT INTO contacto (dni, email, Contacto) VALUES (?, ?, ?)");
$stmt2->bind_param("sss", $dni, $correo_electronico, $telefono_contacto);

$stmt3 = $conn->prepare("INSERT INTO sesiones (dni, email, contrasena) VALUES (?, ?, ?)");
$stmt3->bind_param("sss", $dni, $correo_electronico, $contrasena);

// Ejecutar las consultas
if ($stmt->execute()) {
    // Solo ejecutamos el segundo si el primero tuvo éxito
    if ($stmt2->execute()) {
        // Solo ejecutamos el tercero si el segundo tuvo éxito
        if ($stmt3->execute()) {
            header("Location: register_OK.html");
            exit();
        } else {
            // Redirigir en caso de error en la tercera consulta
            header("Location: campamento-registro-error.html");
            exit();
        }
    } else {
        // Redirigir en caso de error en la segunda consulta
        header("Location: campamento-registro-error.html");
        exit();
    }
} else {
    // Redirigir en caso de error en la primera consulta
    header("Location: campamento-registro-error.html");
    exit();
}

// Cerrar conexiones
$stmt->close();
$stmt2->close();
$stmt3->close();
$conn->close();
?>


