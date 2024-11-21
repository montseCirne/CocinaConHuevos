<?php
include_once "db.php"; // Asegúrate de tener una conexión válida a tu base de datos
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el ID de la receta desde el cuerpo de la solicitud POST
    $recetaId = isset($_POST['receta_id']) ? $_POST['receta_id'] : null;

    // Verificar si el ID de receta está presente y es válido
    if ($recetaId === null || $recetaId <= 0) {
        $response['success'] = false;
        $response['error'] = "ID de receta inválido o no proporcionado.";
        echo json_encode($response);
        exit;
    }

    try {
        // Preparar la consulta para obtener los detalles de la receta
        $stmtReceta = $db->prepare("SELECT * FROM receta WHERE Id = :receta_id");
        $stmtReceta->bindParam(":receta_id", $recetaId, PDO::PARAM_INT);
        $stmtReceta->execute();

        // Verificar si se encontró la receta en la base de datos
        if ($stmtReceta->rowCount() > 0) {
            // Recuperar los detalles de la receta
            $receta = $stmtReceta->fetch(PDO::FETCH_ASSOC);

            // Respuesta exitosa con los datos de la receta
            $response['success'] = true;
            $response['receta'] = $receta;
        } else {
            // No se encontró la receta
            $response['success'] = false;
            $response['error'] = "Receta no encontrada.";
        }
    } catch (PDOException $e) {
        // Capturar errores de base de datos
        $response['success'] = false;
        $response['error'] = "Error de base de datos: " . $e->getMessage();
    }

    // Enviar la respuesta como JSON
    echo json_encode($response);
}
