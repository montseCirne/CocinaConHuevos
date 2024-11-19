<?php
include_once "db.php";

// Configuración de cabeceras para JSON
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
        // Consulta para obtener los datos del perfil del usuario
        $stmtPerfil = $db->prepare("
            SELECT u.nombre, u.apellido, s.correo, s.contraseña 
            FROM usuario u 
            INNER JOIN sesion s ON u.sesion_id = s.id 
            WHERE u.id = :usuario_id
        ");
        $stmtPerfil->bindParam(":usuario_id", $usuarioId, PDO::PARAM_INT);
        $stmtPerfil->execute();

        // Verificar si se encontró un usuario
        if ($stmtPerfil->rowCount() > 0) {
            $perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);

            // Respuesta de éxito con los datos del perfil
            $response['success'] = true;
            $response['perfil'] = $perfil;
        } else {
            // No se encontró el perfil
            $response['success'] = false;
            $response['error'] = "No se encontró información para este usuario.";
        }
    } catch (PDOException $e) {
        // Error en la base de datos
        $response['success'] = false;
        $response['error'] = "Error al recuperar el perfil: " . $e->getMessage();
        error_log($e->getMessage());
    }
} else {
    // Método de solicitud no válido
    $response['success'] = false;
    $response['error'] = "Error: Método de solicitud no válido.";
}

// Enviar la respuesta como JSON
echo json_encode($response);
?>
