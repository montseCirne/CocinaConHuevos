
$(document).ready(function () {
    var userID = sessionStorage.getItem('idU');
    var recetarioID = sessionStorage.getItem('idRecetario');


    $('#Crear').on('click', function(event) {
        event.preventDefault(); // Evita el envío predeterminado del formulario
        verificarCorreoExistente();
    });
    if (userID && userID !== '0') {
        console.log("Se tiene una sesión abierta, ID de usuario: " + userID);
        if (!recetarioID || recetarioID === '0') {
            obtenerRecetario(userID);
            cargarPerfil(userID);
        } else {
            console.log("ID Recetario: " + recetarioID);
        }
    } else {
        console.log("Usuario no autenticado o visitante");
    }

    function cargarPerfil(userID) {
        $.ajax({
            url: '../modelo/recuperarInfo.php',
            method: 'POST',
            data: { usuario_id: userID },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const perfil = response.perfil;
                    console.log(perfil.nombre);
                    // Asignar el nombre y el correo
                    $("#nombre_usuario_perfil").val(perfil.nombre);
                    $("#correo_perfil").val(perfil.correo);
    
                    // Si el apellido es null, asignamos un espacio vacío
                    if (perfil.apellido) {
                        $("#apellido_usuario_perfil").val(perfil.apellido);
                    } else {
                        $("#apellido_usuario_perfil").val('');
                    }
    
                    // Si la foto existe, asignarla
                    if (perfil.foto) {
                        $(".foto_perfil img").attr("src", perfil.foto);
                    }
                } else {
                    console.error("Error al cargar el perfil: " + response.error);
                }
            },
            error: function () {
                console.error("Error en la comunicación con el servidor.");
            }
        });
    }
    
    
    
    // Función para obtener el recetario y guardar su ID en sessionStorage
    function obtenerRecetario(userID) {
        $.ajax({
            url: '../modelo/recuperarRecetario.php',  // Asegúrate de que la URL es correcta
            method: 'POST',  // Verifica que sea POST
            data: { usuario_id: userID },  // Enviar el ID de usuario
            dataType: 'json',  // Esperamos una respuesta JSON
            success: function (response) {
                if (response.success) {
                    sessionStorage.setItem('idRecetario', response.recetario.id); 
                    console.log("ID Recetario guardado: " + response.recetario.id);
                } else {
                    console.error("Error al obtener el recetario: " + response.error);
                }
            },
            
        });
    }
    

    // Función para manejar el inicio de sesión (ya implementada en tu código)
    function iniciarSesion() {
        var user = $("#correo").val().trim();
        var c1 = $("#contrasena").val().trim();
        $.ajax({
            url: '../modelo/login.php',
            method: 'POST',
            data: { user: user, contrasena: c1 },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    sessionStorage.setItem('idU', response.usuario_id);
                    obtenerRecetario(response.usuario_id); 
                    window.location.href = '../vista/sesion.html';
                } else {
                    alert("Usuario o contraseña incorrectos");
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
        sessionStorage.setItem('idRecetario', '0');
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
$(document).ready(function () {
    $("#recetaForm").on("submit", function (e) {
        e.preventDefault(); 
        var formData = new FormData(this); 
        $.ajax({
            url: '../modelo/nuevaReceta.php', 
            method: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,  // No enviar el tipo de contenido
            processData: false,  // No procesar los datos
            success: function (response) {
                if (response.success) {
                    alert("Receta guardada exitosamente.");
                    
                } else {
                    alert("Error: " + response.error);
                }
            },
            error: function () {
                alert("Ocurrió un error al guardar la receta.");
            }
        });
    });
});
