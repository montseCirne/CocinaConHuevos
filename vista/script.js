$(document).ready(() => {
    // Configurar los eventos de las pestañas de categoría
    $('.categoria-tab').on('click', function () {
        const categoria = $(this).data('categoria'); // Obtener la categoría del enlace
        fetchProducts(categoria); // Cargar productos de esa categoría
    });

    // Cargar productos de la categoría por defecto (ejemplo: 'comida')
    fetchProducts("comida");
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
                    <div class="card-content botones">
                        <button class="btn btn-warning" data-product-id="${product.id}">Editar</button>
                        <button class="btn btn-warning" data-product-id="${product.id}">Ver detalles</button>
                    </div>
                </div>
            </div>`;
        $('#Cartas').append(card); // Insertar la carta en el contenedor
    });
};
