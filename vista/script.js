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
        const productId = $(this).data('product-id'); 
        console.log(productId);
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

const fetchProductEdit = (productId) => {
    $.ajax({
        url: '../modelo/detalles.php', 
        method: 'GET',
        dataType: 'json',
        data: { receta_id: productId }, 
        success: (response) => {
            const rec = response.receta;
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

const fetchProductAct = (productId) => {
    const recetaData = JSON.parse(sessionStorage.getItem('recetaSeleccionada')); 
    if (recetaData) {
        const formData = new FormData();
        
        formData.append('id', productId); 
        formData.append('nombre', recetaData.nombre); 
        formData.append('categoria', recetaData.categoria);
        formData.append('tiempo_coccion', recetaData.tiempo_coccion);
        formData.append('ingredientes', recetaData.ingredientes);
        formData.append('descripcion', recetaData.descripcion);
        
        // Si hay una foto, agrégala al FormData
        if (recetaData.foto) {
            formData.append('foto', recetaData.foto); 
        }
        console.log(formData);
        $.ajax({
            url: '../modelo/actualizarReceta.php',
            method: 'POST',
            data: formData,
            processData: false, // No procesar los datos
            contentType: false, // No establecer un tipo de contenido, ya que estamos enviando FormData
            success: (response) => {
                console.log("Respuesta del servidor: ", response);
                if (response.success) {
                    alert('Receta actualizada con éxito.');
                } else {
                    alert(response.message || 'Error al actualizar la receta.');
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error al actualizar la receta:', textStatus, errorThrown);
                alert('Hubo un problema al actualizar la receta.');
            }
        });
    } else {
        alert("No se encontraron los datos de la receta.");
    }
};
