<?php
include_once "db.php";
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recetaId = $_POST['receta_id'];

    // Verificar si el ID de receta está presente
    if ($recetaId <= 0) {
        $response['success'] = false;
        $response['error'] = "ID de receta inválido.";
        echo json_encode($response);
        exit;
    }

    try {
        // Consulta para obtener los datos de la receta
        $stmtReceta = $db->prepare("SELECT * FROM receta WHERE Id = :receta_id");
        $stmtReceta->bindParam(":receta_id", $recetaId, PDO::PARAM_INT);
        $stmtReceta->execute();

        // Verificar si se encontró una receta
        if ($stmtReceta->rowCount() > 0) {
            $receta = $stmtReceta->fetch(PDO::FETCH_ASSOC);

            // Respuesta exitosa
            $response['success'] = true;
            $response['receta'] = $receta;
        } else {
            // No se encontró receta
            $response['success'] = false;
            $response['error'] = "Receta no encontrada.";
        }
    } catch (PDOException $e) {
        // Error en la consulta SQL
        $response['success'] = false;
        $response['error'] = "Error de base de datos: " . $e->getMessage();
    }

    // Enviar la respuesta como JSON
    echo json_encode($response);
}
?>
