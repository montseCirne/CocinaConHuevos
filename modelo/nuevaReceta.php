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
        $tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : '';
        $ingredientes = isset($_POST['ingredientes']) ? $_POST['ingredientes'] : '';
        $recetarioId = isset($_POST['recetarioID']) ? $_POST['recetarioID'] : null;

        // Validar que se proporcione un ID de recetario
        if (is_null($recetarioId)) {
            echo json_encode(['success' => false, 'error' => 'El ID del recetario es obligatorio.']);
            exit();
        }

        // Preparar la consulta SQL para insertar la receta
        $sql = "INSERT INTO receta (nombre, foto, descripcion, categoria, tiempo, ingredientes) 
        VALUES (:nombre, :foto, :descripcion, :categoria, :tiempo, :ingredientes)";

        try {
            // Iniciar una transacción para garantizar la integridad de las inserciones
            $db->beginTransaction();

            // Preparar la consulta
            $stmt = $db->prepare($sql);
            
            // Enlazar los parámetros de la consulta
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':foto', $fotoContenido, PDO::PARAM_LOB);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':tiempo', $tiempo);
            $stmt->bindParam(':ingredientes', $ingredientes);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                // Obtener el ID de la receta recién insertada
                $lastInsertId = $db->lastInsertId();

                // Preparar la consulta para insertar en recetario_receta
                $recetarioSql = "INSERT INTO recetario_receta (recetario_id, receta_id) VALUES (:recetario_id, :receta_id)";
                $recetarioStmt = $db->prepare($recetarioSql);
                $recetarioStmt->bindParam(':recetario_id', $recetarioId);
                $recetarioStmt->bindParam(':receta_id', $lastInsertId);

                // Ejecutar la consulta de recetario_receta
                if ($recetarioStmt->execute()) {
                    // Confirmar la transacción
                    $db->commit();
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Receta y recetario actualizados exitosamente.',
                        'receta_id' => $lastInsertId
                    ]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Error al asociar la receta al recetario.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al insertar la receta.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => 'Error en la consulta: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No se ha subido una foto válida.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud inválida.']);
}
?>
