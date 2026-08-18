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

    // Obtener correo y contraseña
    $correo = trim($datos["correo"] ?? "");
    $password = $datos["password"] ?? "";

    // Validar campos obligatorios
    if ($correo === "" || $password === "") {
        http_response_code(400);

        echo json_encode([
            "success" => false,
            "message" => "El correo y la contraseña son obligatorios."
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

    // Buscar el usuario por correo electrónico
    $consulta = $conexion->prepare(
        "SELECT id, nombre, correo, password, estado
         FROM usuarios
         WHERE correo = :correo
         LIMIT 1"
    );

    $consulta->execute([
        ":correo" => $correo
    ]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    // Verificar que el usuario exista
    if (!$usuario) {
        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "Correo o contraseña incorrectos."
        ]);

        exit;
    }

    // Verificar que el usuario se encuentre activo
    if ((int) $usuario["estado"] !== 1) {
        http_response_code(403);

        echo json_encode([
            "success" => false,
            "message" => "El usuario se encuentra inactivo."
        ]);

        exit;
    }

    // Verificar la contraseña utilizando el hash almacenado
    if (!password_verify($password, $usuario["password"])) {
        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "Correo o contraseña incorrectos."
        ]);

        exit;
    }

    // Autenticación exitosa
    http_response_code(200);

    echo json_encode([
        "success" => true,
        "message" => "Autenticación satisfactoria.",
        "data" => [
            "id" => (int) $usuario["id"],
            "nombre" => $usuario["nombre"],
            "correo" => $usuario["correo"]
        ]
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Error interno al procesar la autenticación."
    ]);
}