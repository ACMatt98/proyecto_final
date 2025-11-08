
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

    //Funcion para obtener el precio del matarial
    async function handleMaterialChange(event) {
        const selectMaterial = event.target;
        const row = selectMaterial.closest('tr');
        const precioInput = row.querySelector('.precio-input');
        const cantidadInput = row.querySelector('.cantidad-input');
        const subtotalInput = row.querySelector('.subtotal-input');
        
        if (!selectMaterial.value) {
            precioInput.value = '';
            subtotalInput.value = '';
            return;
        }
        
        try {
            const response = await fetch('bd/get_precio_material.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_material: parseInt(selectMaterial.value) })
            });
            
            const result = await response.json();
            
            if (result.success) {
                precioInput.value = result.precio_unitario;
                // Guardamos la unidad del precio en el input
                precioInput.dataset.unidad = result.unidad_precio;
                calculateSubtotal({ target: cantidadInput });
            } else {
                precioInput.value = '0.00';
                subtotalInput.value = '0.00';
            }
        } catch (error) {
            console.error('Error al obtener precio:', error);
            precioInput.value = '0.00';
            subtotalInput.value = '0.00';
        }
    }

    //Funcion para calcular el subtotal CON CONVERSIÓN DE UNIDADES
    function calculateSubtotal(event) {
        const input = event.target;
        const row = input.closest('tr');
        
        const cantidadInput = row.querySelector('.cantidad-input');
        const unidadRecetaInput = row.querySelector('.unidad-input');
        const precioInput = row.querySelector('.precio-input');
        const subtotalInput = row.querySelector('.subtotal-input');

        let cantidad = parseFloat(cantidadInput.value) || 0;
        const unidadReceta = unidadRecetaInput.value;
        
        const precioBase = parseFloat(precioInput.value) || 0;
        const unidadPrecio = precioInput.dataset.unidad; // 'Kg', 'gr', 'Lt', 'ml', 'Unidad'

        let precioFinal = precioBase;

        // --- Lógica de Conversión ---
        // De Kilos a Gramos
        if (unidadPrecio === 'Kg' && unidadReceta === 'gr') {
            precioFinal = precioBase / 1000; // Precio por gramo
        }
        // De Litros a Mililitros
        else if (unidadPrecio === 'Lt' && unidadReceta === 'ml') {
            precioFinal = precioBase / 1000; // Precio por mililitro
        }
        // De Gramos a Kilos (menos común, pero posible)
        else if (unidadPrecio === 'gr' && unidadReceta === 'Kg') {
            precioFinal = precioBase * 1000; // Precio por kilo
        }
        // De Mililitros a Litros (menos común)
        else if (unidadPrecio === 'ml' && unidadReceta === 'Lt') {
            precioFinal = precioBase * 1000; // Precio por litro
        }

        subtotalInput.value = (cantidad * precioFinal).toFixed(2);
        
        updateRecipeCost();
    }

    //Funcion para actualizar el costo total
    function updateRecipeCost() {
        const subtotales = document.querySelectorAll('#ingredientesFormBody .subtotal-input');
        let costoTotal = 0;
        
        subtotales.forEach(input => {
            costoTotal += parseFloat(input.value) || 0;
        });
            
        document.querySelector('#costo_receta').value = costoTotal.toFixed(2);
    }                               


    // Ver Ingredientes button handler
    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnIngredientes')) {
            fila = e.target.closest("tr");
            const id_receta = parseInt(fila.cells[0].textContent);
            
            try {
                const response = await fetch('bd/get_ingredientes.php', {
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
        
        // Agregar event listeners
        const selectMaterial = clone.querySelector('.material-select');
        const cantidadInput = clone.querySelector('.cantidad-input');
        
        selectMaterial.addEventListener('change', handleMaterialChange);
        cantidadInput.addEventListener('input', calculateSubtotal);
        
        tbody.appendChild(clone);
    });

    // Eliminar ingrediente
    document.addEventListener('click', (e) => {
            if (e.target.matches('.btnEliminarIngrediente') || e.target.closest('.btnEliminarIngrediente')) {
            const button = e.target.closest('.btnEliminarIngrediente');
            button.closest('tr').remove();
            updateRecipeCost(); // Agregar esta línea
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
                const response = await fetch('bd/get_ingredientes.php', {
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
                
                ingredientes.forEach(async (ingrediente) => {
                    const template = document.querySelector("#templateIngrediente");
                    const clone = template.content.cloneNode(true);
                    
                    const selectMaterial = clone.querySelector('.material-select');
                    const inputCantidad = clone.querySelector('.cantidad-input');
                    const inputUnidad = clone.querySelector('.unidad-input');
                    
                    // AGREGAR EVENT LISTENERS PRIMERO
                    selectMaterial.addEventListener('change', handleMaterialChange);
                    inputCantidad.addEventListener('input', calculateSubtotal);
                    
                    // Seleccionar el material por ID (más confiable que por nombre)
                    selectMaterial.value = ingrediente.id_materiales;
                    inputCantidad.value = ingrediente.cantidad_nec;
                    inputUnidad.value = ingrediente.unidad_medida;
                    
                    tbody.appendChild(clone);
                    
                    // EJECUTAR la función para obtener el precio DESPUÉS de agregar al DOM
                    if (selectMaterial.value) {
                        await handleMaterialChange({ target: selectMaterial });
                    }
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
                    const response = await fetch('/proyecto_final/bd/crud_recetas.php', {
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
    
    // Validación básica
    if (!document.querySelector("#nomb_receta").value.trim()) {
        alert('El nombre de la receta es requerido');
        return;
    }

    // Recolectar ingredientes con validación
    const ingredientes = [];
    let ingredientesValidos = true;
    
    document.querySelectorAll("#ingredientesFormBody tr").forEach(row => {
        const id_materiales = row.querySelector('.material-select').value;
        const cantidad_nec = row.querySelector('.cantidad-input').value;
        const unidad_medida = row.querySelector('.unidad-input').value;
        
        if (!id_materiales || !cantidad_nec || !unidad_medida) {
            ingredientesValidos = false;
            return;
        }
        
        ingredientes.push({
            id_materiales,
            cantidad_nec: parseFloat(cantidad_nec),
            unidad_medida
        });
    });

    if (!ingredientesValidos || ingredientes.length === 0) {
        alert('Todos los ingredientes deben estar completos');
        return;
    }

    const formData = {
        nomb_receta: document.querySelector("#nomb_receta").value.trim(),
        costo_receta: parseFloat(document.querySelector("#costo_receta").value.trim()),
        id_producto_term: document.querySelector("#id_producto_term").value,
        ingredientes,
        id,
        opcion
    };

    try {
        const response = await fetch('/proyecto_final/bd/crud_recetas.php', {   
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();
        
        if (result.success) {
            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
            alert(result.message);

            if (opcion === 1) { // Si es ALTA, agregamos la fila manualmente
                const nuevaFila = result.data;
                tablaRecetas.row.add([
                    nuevaFila.id_receta_estandar,
                    nuevaFila.nomb_receta,
                    nuevaFila.costo_receta,
                    nuevaFila.nomb_producto,
                    '<button class="btn btn-info btn-sm btnIngredientes">Ver Ingredientes</button>', // Botón Ingredientes
                    null  // La columna de acciones se genera por defaultContent
                ]).draw();
            } else { // Si es EDICIÓN o cualquier otro caso de éxito, recargamos la página
                location.reload();
            }

        } else {
            // Si el servidor devuelve success: false, mostramos el error
            throw new Error(result.message);
        }

    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'No se pudo guardar la receta.');
    }
});
});