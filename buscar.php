<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function obtenerDatosPorCedula($cedula) {
    $url = 'https://identidad.mtess.gov.py/alumno/buttonhandler.php';

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

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: Mozilla/5.0'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if (!$response) {
        die(json_encode([
            'error' => 'Error al obtener la respuesta del servidor',
            'detalle' => $error
        ]));
    }

    if ($httpCode !== 200) {
        die(json_encode([
            'error' => "El servidor respondió con código HTTP $httpCode",
            'detalle' => $response
        ]));
    }

    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die(json_encode(['error' => 'Error al decodificar JSON']));
    }

    echo json_encode($data);
}

if (isset($_GET['cedula'])) {
    obtenerDatosPorCedula($_GET['cedula']);
} else {
    echo json_encode(['error' => 'Cédula no proporcionada']);
}
?>
