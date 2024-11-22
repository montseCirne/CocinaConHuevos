<?php
include_once "db.php"; // Archivo que contiene la conexión PDO.
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

$response = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recuperar datos enviados desde el cliente
    $recetaID = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $tiempo = $_POST['tiempo'] ?? null;
    $ingredientes = $_POST['ingredientes'] ?? null;

    // Verificar si se envió una foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    } else {
        $foto = null; 
        echo "No se ha subido una foto o ocurrió un error al subirla.";
    }
    
    

    if ($recetaID) {
        try {
            $db->beginTransaction();

            // Crear la consulta dinámica, excluyendo `foto` si no fue enviada
            $sql_receta = "UPDATE receta 
               SET nombre = COALESCE(:nombre, nombre), 
                   descripcion = COALESCE(:descripcion, descripcion), 
                   tiempo = COALESCE(:tiempo, tiempo), 
                   ingredientes = COALESCE(:ingredientes, ingredientes)";
            if ($foto !== null) {
                $sql_receta .= ", foto = :foto"; 
            }
            $sql_receta .= " WHERE id = :recetaID";

            $stmt_receta = $db->prepare($sql_receta);

            // Asociar los parámetros
            $stmt_receta->bindParam(":nombre", $nombre);
            $stmt_receta->bindParam(":descripcion", $descripcion);
            $stmt_receta->bindParam(":tiempo", $tiempo, PDO::PARAM_INT);
            $stmt_receta->bindParam(":ingredientes", $ingredientes);

            if ($foto !== null) {
                $stmt_receta->bindParam(":foto", $foto, PDO::PARAM_LOB);
            }

            $stmt_receta->bindParam(":recetaID", $recetaID, PDO::PARAM_INT);

            // Ejecutar la consulta
            $stmt_receta->execute();
            $db->commit();

            $response['success'] = true;
            $response['message'] = "Receta actualizada correctamente.";
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $db->rollBack();
            $response['success'] = false;
            $response['error'] = "Error al actualizar receta: " . $e->getMessage();
            error_log("Error al ejecutar la consulta: " . $e->getMessage());
        }
    } else {
        $response['success'] = false;
        $response['error'] = "Faltan parámetros requeridos.";
    }
} else {
    $response['success'] = false;
    $response['error'] = "Método no permitido.";
}

// Devolver la respuesta en formato JSON
echo json_encode($response);
?>
