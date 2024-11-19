<?php
include_once "db.php";  
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar si se ha subido un archivo
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fotoTmp = $_FILES['foto']['tmp_name'];  
        $fotoContenido = file_get_contents($fotoTmp); 
        $fotoNombre = $_FILES['foto']['name'];  

        // Obtener los otros campos
        $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';

        // Preparar la consulta SQL para insertar la receta
        $sql = "INSERT INTO receta (nombre, foto, descripcion, categoria) VALUES (:nombre, :foto, :descripcion, :categoria)";

        try {
            // Preparar la consulta
            $stmt = $db->prepare($sql);
            
            // Enlazar los parámetros de la consulta
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':foto', $fotoContenido, PDO::PARAM_LOB);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':categoria', $categoria);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Receta guardada exitosamente.']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $stmt->errorInfo()]);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error al preparar la consulta: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se ha subido una foto válida.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud inválida.']);
}
?>
