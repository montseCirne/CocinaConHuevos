<?php
include_once "db.php"; 
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar datos enviados desde el cliente
    $userID = $_POST['userID'] ?? null;
    $sesionID = $_POST['sesionID'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $apellido = $_POST['apellido'] ?? null;
    $correo = $_POST['correo'] ?? null;
    $contrasena = $_POST['contrasena'] ?? null;

    if ($userID && $sesionID) {
        try {
            
            $db->beginTransaction();

            // Actualizar los datos en la tabla `usuario`
            if ($nombre || $apellido) {
                $sql_usuario = "UPDATE usuario 
                                SET nombre = COALESCE(:nombre, nombre), 
                                    apellido = COALESCE(:apellido, apellido)
                                WHERE id = :userID";
                $stmt_usuario = $db->prepare($sql_usuario);
                $stmt_usuario->bindParam(":nombre", $nombre);
                $stmt_usuario->bindParam(":apellido", $apellido);
                $stmt_usuario->bindParam(":userID", $userID, PDO::PARAM_INT);
                $stmt_usuario->execute();
            }

            // Actualizar los datos en la tabla `sesion`
            if ($correo || $contrasena) {
                $sql_sesion = "UPDATE sesion 
                               SET correo = COALESCE(:correo, correo), 
                                   contraseña = COALESCE(:contrasena, contraseña)
                               WHERE id = :sesionID";
                $stmt_sesion = $db->prepare($sql_sesion);
                $stmt_sesion->bindParam(":correo", $correo);
                $stmt_sesion->bindParam(":contrasena", $contrasena);
                $stmt_sesion->bindParam(":sesionID", $sesionID, PDO::PARAM_INT);
                $stmt_sesion->execute();
            }

            // Confirmar la transacción
            $db->commit();

            $response['success'] = true;
            $response['message'] = "Perfil actualizado correctamente.";
        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $db->rollBack();
            $response['success'] = false;
            $response['error'] = "Error al actualizar perfil: " . $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['error'] = "Faltan parámetros requeridos.";
    }
} else {
    $response['success'] = false;
    $response['error'] = "Método no permitido.";
}

echo json_encode($response);
?>
