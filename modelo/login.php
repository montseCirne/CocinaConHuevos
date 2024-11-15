<?php 
session_start(); // Iniciar la sesión PHP
include_once "db.php";
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

$response = array(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si se envió un 'user' y 'contrasena', agregarlos al array de respuesta
    if (isset($_POST['user']) && isset($_POST['contrasena'])) {
        $response['user'] = $_POST['user'];
        $response['c1'] = $_POST['contrasena'];

        // Obtener los valores del usuario y la contraseña
        $user = $_POST['user'];
        $c1 = $_POST['contrasena'];

        // Consulta SQL para comprobar si el usuario existe
        $sql = "SELECT id, contraseña FROM sesion WHERE correo = :nombreUser";

        // Preparar la consulta
        $stmt = $db->prepare($sql);

        // Enlazar parámetros
        $stmt->bindParam(":nombreUser", $user, PDO::PARAM_STR);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró algún usuario con el nombre dado
        if ($resultado) {
            $contraseña = $resultado['contraseña'];
            // Si se encontró el usuario, verificar si la contraseña coincide
            if ($contraseña === $c1) {
                // La contraseña coincide
                $response['success'] = "La contraseña coincide";
                // Establecer el ID de sesión en la sesión PHP
                $_SESSION['user_id'] = $resultado['id'];
                // Agregar el ID de sesión a la respuesta
                $response['session_id'] = $_SESSION['user_id'];
            } else {
                // La contraseña no coincide
                $response['error'] = "La contraseña no coincide";
            }
        } else {
            // El usuario no existe en la tabla
            $response['error'] = "El usuario $user no existe en la tabla 'sesion'.";
        }
    } else {
        // Faltan datos de usuario o contraseña
        $response['error'] = "Faltan datos de usuario o contraseña.";
    }
} else {
    $response['error'] = "Error en la solicitud.";
}

echo json_encode($response);
?>
