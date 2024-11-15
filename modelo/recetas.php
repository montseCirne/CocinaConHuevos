<?php
include_once "db.php";

header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

try {
    $db = new PDO($dbInfo, $dbUser, $dbPassword);
    //$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Obtener la categoría desde los parámetros GET
    $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : null;

    // Consultar las recetas, filtrando por categoría si está definida
    $query = "SELECT id, nombre, categoria, descripcion, foto FROM receta";
    if (!is_null($categoria)) {
        $query .= " WHERE categoria = :categoria";
    }

    $statement = $db->prepare($query);

    // Si la categoría está definida, agregar el parámetro de la consulta
    if (!is_null($categoria)) {
        $statement->bindParam(':categoria', $categoria, PDO::PARAM_STR);
    }

    $statement->execute();
    $productos = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Convertir la imagen BLOB en base64
    foreach ($productos as &$producto) {
        if (!is_null($producto['foto'])) {
            $imagenBase64 = base64_encode($producto['foto']);
            $producto['foto'] = 'data:image/jpeg;base64,' . $imagenBase64;
        }
    }

    echo json_encode($productos);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit();
}
?>
