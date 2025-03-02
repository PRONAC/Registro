<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function obtenerDatosPorCedula($cedula) {
    $url = 'https://identidad.mtess.gov.py/alumno/buttonhandler.php';

    // Construcción de los datos a enviar
    $postData = [
        'params' => json_encode([
            'cedula' => $cedula,
            'doctype_id' => '1',
            'table' => 'inscripcion_alumnos.usuario',
            'field' => 'cedula'
        ]),
        'event' => 'cedula_event',
        'pageType' => 'register',
        'masterTable' => '',
        'fieldsData' => json_encode([
            '_register_captcha' => '',
            'cedula' => $cedula,
            'clave' => '',
            'confirm' => '',
            'id_rol' => '3',
            'doctype_id' => '1',
            'nombre' => '',
            'apellido' => '',
            'fechanac' => '',
            'email' => '',
            'tel_celular' => ''
        ])
    ];

    // Inicializar cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Opcional: omitir verificación SSL
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: Mozilla/5.0'
    ]);

    // Ejecutar la solicitud y obtener la respuesta
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtener el código de respuesta HTTP
    $error = curl_error($ch);
    curl_close($ch);

    if (!$response) {
        die(json_encode([
            'error' => 'Error al obtener la respuesta del servidor',
            'detalle' => $error
        ]));
    }

    // Verificar si el código de respuesta HTTP es distinto de 200 (OK)
    if ($httpCode !== 200) {
        die(json_encode([
            'error' => "El servidor respondió con código HTTP $httpCode",
            'detalle' => $response
        ]));
    }

    // Decodificar la respuesta JSON
    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        die(json_encode(['error' => 'Error al decodificar JSON']));
    }

    // Extraer los datos necesarios
    $nombre = $data['nombre'] ?? 'No encontrado';
    $apellido = $data['apellido'] ?? 'No encontrado';
    $fecha_nacimiento = $data['fecha'] ?? 'No encontrado';

    // Formatear la fecha
    if ($fecha_nacimiento !== 'No encontrado') {
        setlocale(LC_TIME, "es_ES.UTF-8", "Spanish_Spain", "es_ES");
        $timestamp = strtotime($fecha_nacimiento);
        $fecha_nacimiento = strftime("%e de %B del %Y", $timestamp);
    }

    // Retornar los datos como JSON
    echo json_encode([
        'nombre' => $nombre,
        'apellido' => $apellido,
        'fecha_nacimiento' => trim($fecha_nacimiento) // Trim para evitar espacios extra
    ]);
}

// Verificar si se pasó la cédula por GET
if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];
    obtenerDatosPorCedula($cedula);
} else {
    echo json_encode(['error' => 'Cédula no proporcionada']);
}
?>
