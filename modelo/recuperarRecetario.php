<?php
include_once "db.php";

// Configuración de cabeceras para JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

// Array para almacenar la respuesta
$response = array();

// Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el ID del usuario enviado desde el frontend
    $usuarioId = $_POST['usuario_id'];

    try {
        // Consulta para obtener el recetario del usuario
        $stmtRecetario = $db->prepare("SELECT * FROM recetario WHERE usuario_id = :usuario_id");
        $stmtRecetario->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmtRecetario->execute();

        // Verificar si se encontró el recetario
        if ($stmtRecetario->rowCount() > 0) {
            $recetario = $stmtRecetario->fetch(PDO::FETCH_ASSOC);

            // Respuesta de éxito con los datos del recetario
            $response['success'] = true;
            $response['recetario'] = $recetario;
        } else {
            // No se encontró el recetario
            $response['success'] = false;
            $response['error'] = "No se encontró un recetario para este usuario.";
        }
    } catch (PDOException $e) {
        // Error en la base de datos
        $response['error'] = "Error al recuperar el recetario: " . $e->getMessage();
        error_log($e->getMessage());
    }
} else {
    // Método de solicitud no válido
    $response['error'] = "Error: Método de solicitud no válido.";
}

// Enviar la respuesta como JSON
echo json_encode($response);
?>
