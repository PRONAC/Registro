<?php
$conexion = new mysqli("localhost", "root", "", "registro040325");

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos");
}

// Verificar si todos los campos están presentes y no están vacíos
if (
    empty($_POST["cedula"]) || empty($_POST["nombre"]) || empty($_POST["apellido"]) ||
    empty($_POST["fecha_nacimiento"]) || empty($_POST["correo"]) ||
    empty($_POST["telefono"]) || empty($_POST["establecimiento"]) || empty($_POST["cargo"])
) {
    die("Error: Todos los campos son obligatorios.");
}

$cedula = trim($_POST["cedula"]);
$nombre = trim($_POST["nombre"]);
$apellido = trim($_POST["apellido"]);
$fecha_nacimiento = trim($_POST["fecha_nacimiento"]); // Se mantiene el formato recibido
$correo = strtolower(trim($_POST["correo"])); // Convertir correo a minúsculas
$telefono = preg_replace("/\s+/", "", trim($_POST["telefono"])); // Eliminar espacios
$establecimiento = strtoupper(trim($_POST["establecimiento"])); // Convertir a mayúsculas
$cargo = strtoupper(trim($_POST["cargo"])); // Convertir a mayúsculas

// Validar formato del correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("Error: El correo electrónico no es válido.");
}

// Validar el número de teléfono y normalizarlo
if (preg_match("/^\+?595(\d{9})$/", $telefono, $matches)) {
    $telefono = "+595 " . substr($matches[1], 0, 3) . " " . substr($matches[1], 3);
} elseif (preg_match("/^0?(\d{9})$/", $telefono, $matches)) {
    $telefono = "+595 " . substr($matches[1], 0, 3) . " " . substr($matches[1], 3);
} else {
    die("Error: Número de teléfono inválido.");
}

// Insertar en la base de datos si todos los datos son válidos
$sql = "INSERT INTO participantes (cedula, nombre, apellido, fecha_nacimiento, correo, telefono, establecimiento, cargo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssssssss", $cedula, $nombre, $apellido, $fecha_nacimiento, $correo, $telefono, $establecimiento, $cargo);

if ($stmt->execute()) {
    echo "Registro exitoso";
} else {
    echo "Error al registrar los datos.";
}

$stmt->close();
$conexion->close();
?>