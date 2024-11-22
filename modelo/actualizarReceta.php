<?php
include_once "db.php"; // Asegúrate de que este archivo incluye la conexión PDO a la base de datos.
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar datos enviados desde el cliente
    $recetaID = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $tiempo = $_POST['tiempo'] ?? null;
    $ingredientes = $_POST['ingredientes'] ?? null;
    $foto = $_POST['foto'] ?? null; // Asegúrate de que se envíe la imagen en base64 si es necesaria

    if ($recetaID) {
        try {
            $db->beginTransaction();

            // Crear la consulta dinámica usando COALESCE para actualizar solo los campos enviados
            $sql_receta = "UPDATE receta 
                           SET nombre = COALESCE(:nombre, nombre), 
                               descripcion = COALESCE(:descripcion, descripcion), 
                               tiempo = COALESCE(:tiempo, tiempo), 
                               ingredientes = COALESCE(:ingredientes, ingredientes), 
                               foto = COALESCE(:foto, foto)
                           WHERE id = :recetaID";
            
            $stmt_receta = $db->prepare($sql_receta);
            $stmt_receta->bindParam(":nombre", $nombre);
            $stmt_receta->bindParam(":descripcion", $descripcion);
            $stmt_receta->bindParam(":tiempo", $tiempo, PDO::PARAM_INT);
            $stmt_receta->bindParam(":ingredientes", $ingredientes);
            $stmt_receta->bindParam(":foto", $foto);
            $stmt_receta->bindParam(":recetaID", $recetaID, PDO::PARAM_INT);
            
            $stmt_receta->execute();

            // Confirmar la transacción
            $db->commit();

            $response['success'] = true;
            $response['message'] = "Receta actualizada correctamente.";
        } catch (Exception $e) {
            // Revertir cambios en caso de error
            $db->rollBack();
            $response['success'] = false;
            $response['error'] = "Error al actualizar receta: " . $e->getMessage();
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
