var userID = sessionStorage.getItem('idU');
var recetarioID = sessionStorage.getItem('idRecetario');

$(document).ready(function () {
    //$("#Crear").on("click", function () {
        //valida();
    //});
    $('#Crear').on('click', function(event) {
        event.preventDefault(); // Evita el envío predeterminado del formulario
        verificarCorreoExistente();
    });
    if (userID && userID !== '0') {
        console.log("Se tiene una sesión abierta, ID de usuario: " + userID);
        console.log("ID Recetario: " + recetarioID);
        //window.location.href = '../vista/sesion.html';
    } else {
        console.log("Usuario no autenticado o visitante");
    }

    // Función para iniciar sesión
    function iniciarSesion() {
        var user = $("#correo").val().trim(); // Obtener el valor del correo
        var c1 = $("#contrasena").val().trim(); // Obtener el valor de la contraseña
        console.log("Intentando iniciar sesión");
        $.ajax({
            url: '../modelo/login.php', 
            method: 'POST',
            data: {
                user: user,
                contrasena: c1
            },
            dataType: 'text', 
            success: function (response) {
                try {
                    response = JSON.parse(response);  
                    if (response.success) {
                        console.log("Inicio de sesión exitoso");
                        sessionStorage.setItem('idU', response.session_id);
                        window.location.href = '../vista/sesion.html';
                        console.log("ID de sesión: " + response.session_id);
                    } else {
                        console.log("Error: " + response.error);
                        alert("Usuario o contraseña incorrectos");
                    }
                } catch (e) {
                    console.error("Error al parsear JSON:", e);
                    alert("Error en la respuesta del servidor.");
                }
            },
            error: function () {
                alert("Error al tratar de enviar datos");
            }
        }); 
               
    }

    // Asignar la función al evento de clic en el botón de envío del formulario
    $("#loginForm").on("submit", function(e) {
        e.preventDefault(); // Evitar el envío del formulario
        iniciarSesion();
    });

    // Función para manejar el estado de sesión (inicio/cierre)
    window.toggleSesion = function() {
        var userID = sessionStorage.getItem('idU');
        if (userID && userID !== '0') {
            cerrarSesion();
        } else {
            window.location.href = '../vista/index.html'; // Redirigir a la vista de inicio si no hay sesión
        }
    };

    // Función para cerrar sesión
    window.cerrarSesion = function() {
        sessionStorage.clear();
        sessionStorage.setItem('idU', '0');
        console.log("Se ha cerrado sesión");
        window.location.href = '../vista/index.html'; // Redirigir al inicio
    };
    

    // Asignar el evento de clic al link de sesión
    $("#sesionLink").on("click", function(e) {
        e.preventDefault();
        toggleSesion();
    });
    
  

    function creaCuenta() {
        var nombre = $("#nombre_usuario_registro").val().trim();
        var correo = $("#correo_registro").val().trim();
        var c1 = $("#contraseña_registro").val().trim();

        console.log("Nombre: ", nombre, "Correo: ", correo, "Contraseña: ", c1);  // Verifica los datos enviados

        if (c1 !== '' && nombre !== '' && correo !== '') {
            $.ajax({
                url: '../modelo/cuentaNueva.php',
                method: 'POST',
                data: {
                    nombre: nombre,
                    correo: correo,
                    contrasena: c1
                },
                dataType: 'json',
                success: function (response) {
                    console.log(response);  // Verifica la respuesta del servidor
                    response.success
                        // Almacenar los IDs de usuario y recetario en sessionStorage
                    sessionStorage.setItem('idU', response.id);  // Guardar el ID del usuario
                    sessionStorage.setItem('idRecetario', response.recetario_id);  // Guardar el ID del recetario
                    console.log("ID de usuario: " + response.id);
                    console.log("ID de recetario: " + response.recetario_id);
                    alert("Cuenta creada exitosamente. ID de usuario: " + response.id + ", ID de recetario: " + response.recetario_id);
                    window.location.href = 'loggin.html';  // Redirigir a la página de login
                    
                },
                
            });
        } else {
            alert("Todos los campos deben estar llenos.");
        }
    }
    // Función para verificar si el correo ya está registrado
    function verificarCorreoExistente() {
        var correo = $("#correo_registro").val().trim();

        if (correo !== '') {
            $.ajax({
                url: '../modelo/verificarCorreo.php',  // Ruta del archivo PHP
                method: 'POST',
                data: {
                    correo: correo
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // Si el correo no está registrado, continuar con la creación de cuenta
                        creaCuenta();
                    } else {
                        // Si el correo ya está registrado, mostrar el error
                        alert(response.error);
                    }
                },
                error: function () {
                    alert("Error al verificar el correo.");
                }
            });
        } else {
            alert("Por favor, ingresa un correo electrónico.");
        }
    }

});
