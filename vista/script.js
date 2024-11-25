$(document).ready(() => {
    $('.categoria-tab').on('click', function () {
        const categoria = $(this).data('categoria'); 
        fetchProducts(categoria); 
    });

    fetchProducts("comida");

    $(document).on('click', '#ver-detalles', function () {
        const productId = $(this).data('product-id'); 
        console.log(productId);
        fetchProductDetails(productId); 
    });

    $(document).on('click', '#editar', function () {
        const productId = $(this).data('product-id'); 
        console.log(productId);
        fetchProductEdit(productId); 
    });

    $(document).on('click', '#actualizar_receta', function () {
        const productId = recetaSeleccionada.id; 
        fetchProductAct(productId); 
    });

    const recetaSeleccionada = JSON.parse(sessionStorage.getItem('recetaSeleccionada'));

    if (recetaSeleccionada) {
        $('#nombreR').val(recetaSeleccionada.nombre);
        $('#categoria').val(recetaSeleccionada.categoria);
        $('#tiempo').val(recetaSeleccionada.tiempo_coccion); 
        $('#ingredientes').val(recetaSeleccionada.ingredientes);
        $('#descripcion').val(recetaSeleccionada.descripcion);
        if (recetaSeleccionada.foto) {
            $('#foto-preview').attr('src', recetaSeleccionada.foto).show(); // Agrega una vista previa
        }
        $('#actualizar_receta').data('product-id', recetaSeleccionada.id);
    }
});

const fetchProducts = (categoria = null) => {
    $.ajax({
        url: '../modelo/recetas.php',
        method: 'GET',
        dataType: 'json',
        data: { categoria }, // Enviar la categoría como parámetro GET
        success: (response) => {
            if (response.error) {
                console.error(response.error);
            } else {
                generateCards(response); // Generar las cartas de productos
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            console.error('AJAX error: ', textStatus, errorThrown);
        }
    });
};

const generateCards = (products) => {
    $('#Cartas').empty(); // Limpiar los productos anteriores
    products.forEach(product => {
        const card = `
            <div class="col s12">
                <div class="card">
                    <div class="card-image">
                        <img src="${product.foto}" alt="${product.nombre}">
                        <span class="card-title">${product.nombre}</span>
                    </div>
                    <div class="card-content">
                        <button class="btn btn-warning" id="editar" data-product-id="${product.id}">Editar</button>
                        <button class="btn btn-warning" id="ver-detalles" data-product-id="${product.id}">Ver detalles</button>
                    </div>
                </div>
            </div>`;
        $('#Cartas').append(card); // Insertar la carta en el contenedor
    });
};

// Función para obtener los detalles de una receta
const fetchProductEdit = (productId) => {
    $.ajax({
        url: '../modelo/detalles.php', 
        method: 'GET',
        dataType: 'json',
        data: { receta_id: productId }, 
        success: (response) => {
            const rec = response.receta;
            console.log(rec.id)
            if (response.error) {
                console.error(response.error);
            } else {
                sessionStorage.setItem('recetaSeleccionada', JSON.stringify(rec));
                console.log("Receta recuperada:", rec);
                window.location.href = 'editRecipe.html'; 
            }
        },
        
        error: (jqXHR, textStatus, errorThrown) => {
            console.error('AJAX error: ', textStatus, errorThrown);
            alert("Hubo un problema al recuperar los detalles de la receta. Inténtalo de nuevo.");
        }
    }); 
};

const fetchProductDetails = (productId) => {
    $.ajax({
        url: '../modelo/detalles.php', // Suponiendo que esta es la URL para obtener los detalles de la receta
        method: 'GET',
        dataType: 'json',
        data: { receta_id: productId }, // Enviar el ID del producto como parámetro GET
        success: (response) => {
            const rec = response.receta;
            if (response.error) {
                console.error(response.error);
            } else {
                sessionStorage.setItem('recetaSeleccionada', JSON.stringify(rec));
                console.log("Receta recuperada:", rec);
                window.location.href = 'recipe_details.html'; 
            }
        },
        
        error: (jqXHR, textStatus, errorThrown) => {
            console.error('AJAX error: ', textStatus, errorThrown);
            alert("Hubo un problema al recuperar los detalles de la receta. Inténtalo de nuevo.");
        }
    }); 
};


$(document).ready(function () {
    $('#recetaEdit').on('submit', function (event) {
        event.preventDefault(); 
        let formData = new FormData(this);
        console.log(formData);
        $.ajax({
            url: '../modelo/actualizarReceta.php', 
            type: 'POST',
            data: formData,
            contentType: false, 
            processData: false, 
            success: function (response) {
                if (response.success) {
                    alert('Receta actualizada correctamente');
                    // Redirigir o actualizar lista de recetas
                    window.location.href = "index.html"; // Cambiar a la página de inicio o listado de recetas
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function (xhr, status, error) {
                alert('Error en la petición: ' + error);
            }
        });
    });
});

const fetchProductAct = (productId) => {
    const form = document.querySelector('#recetaEdit'); 
    const formData = new FormData(form); 
    formData.append("id", productId);

    console.log("EDITAR", formData);
    $.ajax({
        url: '../modelo/actualizarReceta.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: (response) => {
            console.log("Respuesta del servidor: ", response);
            if (response.success) {
                alert('Receta actualizada con éxito.');
                window.location.href = "sesion.html"; 
            } else {
                alert(response.message || 'Error al actualizar la receta.');
            }
        },
        error: (jqXHR, textStatus, errorThrown) => {
            console.error('Error al actualizar la receta:', textStatus, errorThrown);
            alert('Hubo un problema al actualizar la receta.');
        }
    });
};

