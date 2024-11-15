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
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contrasena'];

    try {
        // Iniciar la transacción
        if (!$db->beginTransaction()) {
            throw new Exception("No se pudo iniciar la transacción.");
        }

        // Insertar sesión
        $stmtUsuario = $db->prepare("INSERT INTO sesion (correo, contraseña) VALUES (:correo, :contrasena)");
        $stmtUsuario->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmtUsuario->bindParam(":contrasena", $contraseña, PDO::PARAM_STR);
        $stmtUsuario->execute();

        // Obtener el último ID insertado
        $idSesion = $db->lastInsertId();

        // Insertar el usuario relacionado con la sesión
        $stmtSesion = $db->prepare("INSERT INTO usuario (nombre, sesion_id) VALUES (:nombre, :sesion_id)");
        $stmtSesion->bindParam(":nombre", $nombre, PDO::PARAM_STR);
        $stmtSesion->bindParam(":sesion_id", $idSesion, PDO::PARAM_INT);
        $stmtSesion->execute();

        // Obtener el ID del usuario
        $idUsuario = $db->lastInsertId();

        // Insertar el recetario relacionado con el usuario
        $stmtRecetario = $db->prepare("INSERT INTO recetario (usuario_id) VALUES (:usuario_id)");
        $stmtRecetario->bindParam(":usuario_id", $idUsuario, PDO::PARAM_INT);
        $stmtRecetario->execute();

        // Obtener el ID del recetario antes de hacer commit
        $idRecetario = $db->lastInsertId();

        // Verificar si la transacción sigue activa antes de hacer commit
        if ($db->inTransaction()) {
            $db->commit();
        } else {
            throw new Exception("La transacción no está activa antes de hacer commit.");
        }

        // Respuesta de éxito
        $response['success'] = true;
        $response['id'] = $idUsuario;
        $response['recetario_id'] = $idRecetario;

    } catch (PDOException $e) {
        // En caso de error, revertir la transacción si está activa
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $response['error'] = "Error al crear la cuenta: " . $e->getMessage();
        error_log($e->getMessage());
    } catch (Exception $e) {
        // Capturar cualquier otro error
        $response['error'] = "Error en la transacción: " . $e->getMessage();
        error_log($e->getMessage());
    }
} else {
    // Si la solicitud no es de tipo POST, enviar un mensaje de error
    $response['error'] = "Error: Método de solicitud no válido.";
}

// Enviar la respuesta como JSON
echo json_encode($response);
?>
