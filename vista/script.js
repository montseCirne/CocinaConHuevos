$(document).ready(() => {
    // Configurar los eventos de las pestañas de categoría
    $('.categoria-tab').on('click', function () {
        const categoria = $(this).data('categoria'); // Obtener la categoría del enlace
        fetchProducts(categoria); // Cargar productos de esa categoría
    });

    // Cargar productos de la categoría por defecto (ejemplo: 'comida')
    fetchProducts("comida");

    $(document).on('click', '#ver-detalles', function () {
        const productId = $(this).data('product-id'); // Obtener el ID del producto
        console.log(productId);
        fetchProductDetails(productId); // Obtener los detalles de la receta
    });
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
                        <button class="btn btn-warning" data-product-id="${product.id}">Editar</button>
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
            console.log('Response from server:', response); // Agrega esto para ver la respuesta
            if (response.error) {
                console.error(response.error);
            } else {
                sessionStorage.setItem('recetaSeleccionada', JSON.stringify(response));
                console.log("Receta recuperada:", response);
            }
        },
        
        error: (jqXHR, textStatus, errorThrown) => {
            console.error('AJAX error: ', textStatus, errorThrown);
            alert("Hubo un problema al recuperar los detalles de la receta. Inténtalo de nuevo.");
        }
    });
    
};

