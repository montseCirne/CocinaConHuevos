<?php
include_once "db.php";  // Asegúrate de tener correctamente configurada tu conexión a la base de datos

// Configuración de cabeceras para JSON
header("Content-Type: application/json");
header("Cache-Control: no-cache, private");
header("Pragma: no-cache");

// Array para almacenar la respuesta
$response = array();

// Verificar si la solicitud es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el correo del formulario
    $correo = $_POST['correo'];

    try {
        // Preparar la consulta para verificar si el correo ya está registrado
        $stmt = $db->prepare("SELECT COUNT(*) FROM sesion WHERE correo = :correo");
        $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el resultado de la consulta
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Si el correo ya está registrado
            $response = array("success" => false, "error" => "El correo electrónico ya está registrado.");
        } else {
            // Si el correo no está registrado
            $response = array("success" => true);
        }
    } catch (PDOException $e) {
        // En caso de error
        $response['error'] = "Error al verificar el correo: " . $e->getMessage(); 
        error_log($e->getMessage());
    }
} else {
    // Si la solicitud no es de tipo POST, enviar un mensaje de error
    $response['error'] = "Error: Método de solicitud no válido.";
}

// Enviar la respuesta como JSON
echo json_encode($response);
?>
