<?php
include_once "db.php";  
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

try {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        
        // Consulta SQL para obtener los detalles de la receta
        $stmt = $pdo->prepare("SELECT * FROM recetas WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            echo json_encode($product, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => 'Receta no encontrada']);
        }
    } else {
        echo json_encode(['error' => 'ID de receta no válido']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la conexión a la base de datos: ' . $e->getMessage()]);
}

?>
