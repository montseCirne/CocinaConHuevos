<?php
include_once "db.php";  
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

// Verificar si el método de la solicitud es GET y si se proporciona un ID
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['receta_id'])) {
    $recetaId = $_GET['receta_id'];

    // Preparar la consulta SQL para recuperar la receta
    $sql = "SELECT nombre, foto, descripcion, categoria, tiempo, ingredientes 
            FROM receta WHERE Id = :receta_id";

    try {
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':receta_id', $recetaId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $receta = $stmt->fetch(PDO::FETCH_ASSOC);

            // Codificar la imagen BLOB como Base64
            if ($receta['foto']) {
                $receta['foto'] = 'data:image/jpeg;base64,' . base64_encode($receta['foto']);
            }

            echo json_encode([
                'success' => true,
                'receta' => $receta
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Receta no encontrada.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud inválida o ID de receta no proporcionado.']);
}
?>