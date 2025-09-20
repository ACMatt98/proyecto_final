document.addEventListener('DOMContentLoaded', () => {
    let tablaComprobantes;
    let fila;
    let id = null;
    let opcion;

    // Initialize DataTable
    tablaComprobantes = new DataTable("#tablaComprobantes", {
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

    // Agregar nuevo material usando template
    document.querySelector('#btnAgregarMaterial').addEventListener('click', () => {
        const template = document.querySelector('#templateMaterial');
        const tbody = document.querySelector('#materialesCompraBody');
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
    });

    function calcularPrecioTotal() {
        let total = 0;
        document.querySelectorAll("#materialesCompraBody tr").forEach(row => {
            const precio_unitario = parseFloat(row.querySelector('.precio-input').value) || 0;
            total += precio_unitario;
        });
        document.querySelector("#precio_total").value = total;
    }

    // Listener para cambios en cantidad y precio unitario
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-input') || e.target.classList.contains('precio-input')) {
            calcularPrecioTotal();
        }
    });

    // Listener para recalcular al agregar material
    document.querySelector('#btnAgregarMaterial').addEventListener('click', () => {
        setTimeout(calcularPrecioTotal, 100); // Espera a que el DOM se actualice
    });

    // Listener para recalcular al eliminar material
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEliminarMaterial') || e.target.closest('.btnEliminarMaterial')) {
            setTimeout(calcularPrecioTotal, 100);
        }
    });







    // Cuando se selecciona un material, mostrar la unidad automáticamente
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('material-select')) {
            const unidad = e.target.selectedOptions[0].getAttribute('data-unidad') || '';
            e.target.closest('tr').querySelector('.unidad-input').value = unidad;
        }
    });

    // Eliminar material
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEliminarMaterial') || e.target.closest('.btnEliminarMaterial')) {
            const button = e.target.closest('.btnEliminarMaterial');
            button.closest('tr').remove();
        }
    });

    // Handler para borrar comprobante
    document.addEventListener('click', async (e) => {
    if (e.target.matches('.btnBorrar')) {
        fila = e.target.closest("tr");
        id = parseInt(fila.cells[0].textContent);
        opcion = 3; // borrar

        if (confirm(`¿Está seguro de eliminar el comprobante: ${id}?`)) {
            try {
                const response = await fetch('bd/crud_comprobante_compra.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ opcion, id })
                });
                const result = await response.json();
                if (result.success) {
                    tablaComprobantes.row(fila).remove().draw();
                } else {
                    alert(result.message || 'Error al borrar el comprobante');
                }
            } catch (error) {
                alert('Error al borrar el comprobante');
            }
        }
    }
});

    // Nuevo button handler
    document.querySelector("#btnNuevo").addEventListener('click', () => {
        document.querySelector("#formComprobantes").reset();
        document.querySelector("#materialesCompraBody").innerHTML = '';
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Comprobante";
        $('#modalCRUD').modal('show');
        id = null;
        opcion = 1; // alta
    });

    // Edit button handler
    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnEditar')) {
            fila = e.target.closest("tr");
            id = parseInt(fila.cells[0].textContent);
            
            // Obtener datos del comprobante
            const fecha = fila.cells[1].textContent;
            const n_de_comprob = fila.cells[2].textContent;
            const precio_total = fila.cells[3].textContent;
            const proveedor = fila.cells[4].textContent;

            document.querySelector("#fecha").value = fecha;
            document.querySelector("#n_de_comprob").value = n_de_comprob;
            document.querySelector("#precio_total").value = precio_total;
            
            // Seleccionar proveedor correcto
            const proveedorSelect = document.querySelector("#id_proveedor");
            Array.from(proveedorSelect.options).forEach(option => {
                if (option.text === proveedor) option.selected = true;
            });

            // Seleccionar tipo de factura correcto
            const tipo_factura = fila.cells[5].textContent.trim(); // Ajusta el índice si es necesario
            document.querySelector("#factura_global").value = tipo_factura;

            // Cargar materiales existentes
            try {
                const response = await fetch('bd/get_materiales_compra.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_comprobante: id })
                });

                if (!response.ok) throw new Error('Error al cargar materiales');
                
                const materiales = await response.json();
                const tbody = document.querySelector("#materialesCompraBody");
                tbody.innerHTML = '';
                
                materiales.forEach(material => {
                    const template = document.querySelector("#templateMaterial");
                    const clone = template.content.cloneNode(true);
                    
                    const selectMaterial = clone.querySelector('.material-select');
                    const inputCantidad = clone.querySelector('.cantidad-input');
                    const inputUnidad = clone.querySelector('.unidad-input');
                    const inputPrecio = clone.querySelector('.precio-input');
                    
                    Array.from(selectMaterial.options).forEach(option => {
                        if (option.text === material.nombre_material) option.selected = true;
                    });
                    
                    inputCantidad.value = material.cantidad;
                    inputUnidad.value = material.unidad_medida || '';
                    inputPrecio.value = material.precio_unitario;
                    
                    tbody.appendChild(clone);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los materiales del comprobante');
            }
            calcularPrecioTotal();
            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Comprobante";
            $('#modalCRUD').modal('show');
        }
    });

    // Delete button handler 
    document.addEventListener('click', (e) => {
        if (e.target.closest('.btnEliminarMaterial')) {
            e.target.closest('tr').remove();
        }
    });

    // Form submit handler con unidad y cantidad
    document.querySelector("#formComprobantes").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Validación básica
        if (!document.querySelector("#fecha").value) {
            alert('La fecha es requerida');
            return;
        }

        if (!document.querySelector("#n_de_comprob").value.trim()) {
            alert('El número de comprobante es requerido');
            return;
        }

        // Obtener el valor de factura global ANTES del bucle
        const tipo_factura = document.querySelector('#factura_global').value;
        
        // Recolectar materiales con validación
        const materiales = [];
        let materialesValidos = true;
        
        document.querySelectorAll("#materialesCompraBody tr").forEach(row => {
            const id_materiales = row.querySelector('.material-select').value;
            const cantidad = row.querySelector('.cantidad-input').value;
            const unidad_medida = row.querySelector('.unidad-input').value;
            const precio_unitario = row.querySelector('.precio-input').value;

            if (!id_materiales || !cantidad || !precio_unitario ||!unidad_medida || !tipo_factura) {
                materialesValidos = false;
                return;
            }
            
            materiales.push({
                id_materiales,
                cantidad: parseFloat(cantidad),
                unidad_medida,
                precio_unitario: parseFloat(precio_unitario),
                tipo_factura
            });
        });

        if (!materialesValidos || materiales.length === 0) {
            alert('Todos los materiales deben estar completos y debe haber al menos uno');
            return;
        }

        
        const formData = {
            fecha: document.querySelector("#fecha").value,
            n_de_comprob: document.querySelector("#n_de_comprob").value.trim(),
            precio_total: parseFloat(document.querySelector("#precio_total").value.trim()),
            id_proveedor: document.querySelector("#id_proveedor").value,
            materiales,
            id,
            opcion,
            tipo_factura
        };

        try {
            const response = await fetch('bd/crud_comprobante_compra.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message);
            }
            
            // Recargar la tabla
            location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al guardar los datos');
        }
    });
});