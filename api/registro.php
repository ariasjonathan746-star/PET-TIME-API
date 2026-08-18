<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "config/database.php";

try {

    // Verificar que la solicitud utilice el método POST
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Método no permitido. Utilice POST."
        ]);

        exit;
    }

    // Obtener los datos enviados en formato JSON
    $datos = json_decode(file_get_contents("php://input"), true);

    // Verificar que se haya recibido un JSON válido
    if (!is_array($datos)) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Los datos enviados no tienen un formato JSON válido."
        ]);

        exit;
    }

    // Obtener los datos
    $nombre = trim($datos["nombre"] ?? "");
    $correo = trim($datos["correo"] ?? "");
    $password = $datos["password"] ?? "";

    // Validar campos obligatorios
    if ($nombre === "" || $correo === "" || $password === "") {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "Todos los campos son obligatorios."
        ]);

        exit;
    }

    // Validar formato del correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "El correo electrónico no tiene un formato válido."
        ]);

        exit;
    }

    // Validar longitud mínima de la contraseña
    if (strlen($password) < 6) {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "La contraseña debe tener mínimo 6 caracteres."
        ]);

        exit;
    }

    // Verificar si el correo ya está registrado
    $consulta = $conexion->prepare(
        "SELECT id FROM usuarios WHERE correo = :correo LIMIT 1"
    );

    $consulta->execute([
        ":correo" => $correo
    ]);

    if ($consulta->fetch()) {
        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" => "El correo electrónico ya se encuentra registrado."
        ]);

        exit;
    }

    // Generar hash seguro de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Registrar el usuario
    $insertar = $conexion->prepare(
        "INSERT INTO usuarios (nombre, correo, password)
         VALUES (:nombre, :correo, :password)"
    );

    $insertar->execute([
        ":nombre" => $nombre,
        ":correo" => $correo,
        ":password" => $passwordHash
    ]);

    // Obtener el ID generado
    $idUsuario = $conexion->lastInsertId();

    // Respuesta exitosa
    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Usuario registrado correctamente.",
        "data" => [
            "id" => (int) $idUsuario,
            "nombre" => $nombre,
            "correo" => $correo
        ]
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Error interno al procesar el registro."
    ]);
}