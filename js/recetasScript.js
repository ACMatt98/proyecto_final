document.addEventListener('DOMContentLoaded', () => {
    let tablaRecetas;
    let fila;
    let id = null;
    let opcion;

    // Initialize DataTable
    tablaRecetas = new DataTable("#tablaRecetas", {
        columnDefs: [{
            targets: -1,
            data: null,
            defaultContent: `<div class='text-center'>
                <div class='btn-group'>
                    <button class='btn btn-primary btnEditar'>Editar</button>
                    <button class='btn btn-danger btnBorrar'>Borrar</button>
                </div>
            </div>`
        }],
        language: {
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            },
            sProcessing: "Procesando..."
        }
    });

    // Ver Ingredientes button handler
    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnIngredientes')) {
            fila = e.target.closest("tr");
            const id_receta = parseInt(fila.cells[0].textContent);
            
            try {
                const response = await fetch('../bd/get_ingredientes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_receta })
                });

                if (!response.ok) throw new Error('Network response was not ok');
                
                const ingredientes = await response.json();
                const tbody = document.querySelector("#ingredientesBody");
                tbody.innerHTML = '';
                
                ingredientes.forEach(ingrediente => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${ingrediente.nombre_material}</td>
                            <td>${ingrediente.cantidad_nec}</td>
                            <td>${ingrediente.unidad_medida}</td>
                        </tr>
                    `;
                });

                const modalIngredientes = new bootstrap.Modal(document.querySelector("#modalIngredientes"));
                modalIngredientes.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los ingredientes');
            }
        }
    });

    // Agregar nuevo ingrediente
    document.querySelector("#btnAgregarIngrediente").addEventListener('click', () => {
        const template = document.querySelector("#templateIngrediente");
        const tbody = document.querySelector("#ingredientesFormBody");
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
    });

    // Eliminar ingrediente
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEliminarIngrediente') || e.target.closest('.btnEliminarIngrediente')) {
            const button = e.target.closest('.btnEliminarIngrediente');
            button.closest('tr').remove();
        }
    });

    // Nuevo button handler
    document.querySelector("#btnNuevo").addEventListener('click', () => {
        document.querySelector("#formRecetas").reset();
        document.querySelector("#ingredientesFormBody").innerHTML = '';
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nueva Receta";
        const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
        modalCRUD.show();
        id = null;
        opcion = 1; // alta
    });

    // Edit button handler
    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnEditar')) {
            fila = e.target.closest("tr");
            id = parseInt(fila.cells[0].textContent);
            const nomb_receta = fila.cells[1].textContent;
            const costo_receta = fila.cells[2].textContent;
            const producto = fila.cells[3].textContent;

            document.querySelector("#nomb_receta").value = nomb_receta;
            document.querySelector("#costo_receta").value = costo_receta;
            
            const selectProducto = document.querySelector("#id_producto_term");
            Array.from(selectProducto.options).forEach(option => {
                if (option.text === producto) option.selected = true;
            });

            // Cargar ingredientes existentes
            try {
                const response = await fetch('../bd/get_ingredientes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_receta: id })
                });

                if (!response.ok) throw new Error('Network response was not ok');
                
                const ingredientes = await response.json();
                const tbody = document.querySelector("#ingredientesFormBody");
                tbody.innerHTML = '';
                
                ingredientes.forEach(ingrediente => {
                    const template = document.querySelector("#templateIngrediente");
                    const clone = template.content.cloneNode(true);
                    
                    const selectMaterial = clone.querySelector('.material-select');
                    const inputCantidad = clone.querySelector('.cantidad-input');
                    const inputUnidad = clone.querySelector('.unidad-input');
                    
                    Array.from(selectMaterial.options).forEach(option => {
                        if (option.text === ingrediente.nombre_material) option.selected = true;
                    });
                    
                    inputCantidad.value = ingrediente.cantidad_nec;
                    inputUnidad.value = ingrediente.unidad_medida;
                    
                    tbody.appendChild(clone);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los ingredientes');
            }

            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Receta";
            const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
            modalCRUD.show();
        }
    });

    // Delete button handler
    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnBorrar')) {
            fila = e.target.closest("tr");
            id = parseInt(fila.cells[0].textContent);
            opcion = 3; // borrar
            
            if (confirm(`¿Está seguro de eliminar la receta: ${id}?`)) {
                try {
                    const response = await fetch('../bd/crud_recetas.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    tablaRecetas.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar la receta');
                }
            }
        }
    });

    // Form submit handler
    document.querySelector("#formRecetas").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Recolectar ingredientes
        const ingredientes = [];
        document.querySelectorAll("#ingredientesFormBody tr").forEach(row => {
            ingredientes.push({
                id_materiales: row.querySelector('.material-select').value,
                cantidad_nec: row.querySelector('.cantidad-input').value,
                unidad_medida: row.querySelector('.unidad-input').value
            });
        });

        const formData = {
            nomb_receta: document.querySelector("#nomb_receta").value.trim(),
            costo_receta: parseFloat(document.querySelector("#costo_receta").value.trim()),
            id_producto_term: document.querySelector("#id_producto_term").value,
            ingredientes,
            id,
            opcion
        };

        try {
            const response = await fetch('../bd/crud_recetas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const { id_receta_estandar: newId, nomb_receta, costo_receta, nomb_producto } = data[0];

            if (opcion === 1) {
                tablaRecetas.row.add([
                    newId, 
                    nomb_receta, 
                    costo_receta, 
                    nomb_producto,
                    '<button class="btn btn-info btnIngredientes">Ver Ingredientes</button>',
                    ''
                ]).draw();
            } else {
                tablaRecetas.row(fila).data([
                    newId, 
                    nomb_receta, 
                    costo_receta, 
                    nomb_producto,
                    '<button class="btn btn-info btnIngredientes">Ver Ingredientes</button>',
                    ''
                ]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});