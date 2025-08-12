let productosSeleccionados = [];  //Es para calcular monto total en presupuesto nuevo

document.addEventListener('DOMContentLoaded', () => {
    let tablaPresupuestos;
    let fila;
    let id = null;
    let opcion;

    // Initialize DataTable
    tablaPresupuestos = new DataTable("#tablaPresupuestos", {
        columnDefs: [
            {
                targets: 0,
                visible: false
            }
        ],
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

    // Función para calcular el total
    function calcularTotal() {
        let subtotal = productosSeleccionados.reduce((sum, producto) => sum + producto.precio, 0);
        let manoObra = parseFloat(document.querySelector("#mano_obra").value) || 0;
        let total = subtotal + manoObra;
        document.querySelector("#precio_total_presup").value = total.toFixed(2);
    }

    // Evento para agregar productos
    document.querySelector("#btn-agregar-producto").addEventListener('click', () => {
    const select = document.querySelector("#select-productos");
    const productoId = select.value;
    const productoTexto = select.options[select.selectedIndex].text;
    const productoPrecio = parseFloat(select.options[select.selectedIndex].dataset.precio);
    
    if (!productosSeleccionados.some(p => p.id === productoId)) {
        productosSeleccionados.push({
            id: productoId,
            nombre: productoTexto.split(' - ')[0],
            precio: productoPrecio
        });
        
        const contenedor = document.querySelector("#contenedor-productos");
        const productoElement = document.createElement('div');
        productoElement.className = 'd-flex justify-content-between align-items-center mb-2';
        productoElement.innerHTML = `
            <span>${productoTexto}</span>
            <button type="button" class="btn btn-sm btn-danger btn-eliminar-producto" data-id="${productoId}">
                <i class="bi bi-trash"></i>
            </button>
        `;
        contenedor.appendChild(productoElement);
        
        calcularTotal();
    }
    });

    // Evento para eliminar productos
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btn-eliminar-producto')) {
            const productoId = e.target.dataset.id;
            productosSeleccionados = productosSeleccionados.filter(p => p.id !== productoId);
            e.target.closest('div').remove();
            calcularTotal();
        }
    });

    // Evento para cambios en mano de obra
    document.querySelector("#mano_obra").addEventListener('input', calcularTotal);




    // Nuevo button handler
    document.querySelector("#btnNuevo").addEventListener('click', () => {
        document.querySelector("#formPresupuestos").reset();
        document.querySelector("#modalTitle").textContent = "Nuevo Presupuesto";
        const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
        modalCRUD.show();
        id = null;
        opcion = 1; // alta
    });

    // Edit button handler
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEditar')) {
            fila = e.target.closest("tr");
            const datos = tablaPresupuestos.row(fila).data();
            id = parseInt(datos[0]);
            
            // Llenar el formulario con los datos
            document.querySelector("#id_cliente").value = datos[1]; // Ajustar según necesidad
            document.querySelector("#fecha_presup").value = datos[2];
            document.querySelector("#precio_total_presup").value = parseFloat(datos[3].replace('$', ''));
            document.querySelector("#id_estado_presupuesto").value = datos[4]; // Ajustar según necesidad
            
            document.querySelector("#modalTitle").textContent = "Editar Presupuesto";
            const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
            modalCRUD.show();
            opcion = 2; // editar
        }
    });

    // Delete button handler
    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnBorrar')) {
            fila = e.target.closest("tr");
            const datos = tablaPresupuestos.row(fila).data();
            id = parseInt(datos[0]);
            opcion = 3; // borrar
            
            if (confirm(`¿Está seguro de eliminar el presupuesto para ${datos[1]}?`)) {
                try {
                    const response = await fetch('bd/crud_presupuestos.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `opcion=${opcion}&id=${id}`
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    tablaPresupuestos.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el presupuesto');
                }
            }
        }
    });

    // Form submit handler
    document.querySelector("#formPresupuestos").addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('fecha_presup', document.querySelector("#fecha_presup").value);
    formData.append('lugar', document.querySelector("#lugar").value);
    formData.append('precio_total_presup', document.querySelector("#precio_total_presup").value);
    formData.append('mano_obra', document.querySelector("#mano_obra").value);
    formData.append('id_estado_presupuesto', document.querySelector("#id_estado_presupuesto").value);
    formData.append('id_cliente', document.querySelector("#id_cliente").value);
    formData.append('observaciones', document.querySelector("#observaciones").value);
    formData.append('productos', JSON.stringify(productosSeleccionados));
    formData.append('opcion', opcion);
    if (id) formData.append('id', id);

        try {
            const response = await fetch('bd/crud_presupuestos.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const presupuesto = data[0];
            
            const rowData = [
                presupuesto.id_presupuesto,
                `${presupuesto.nombre_cliente} ${presupuesto.apellido_cliente}`,
                presupuesto.fecha_presup,
                `$${parseFloat(presupuesto.precio_total_presup).toFixed(2)}`,
                presupuesto.estado,
                `<div class='text-center'><div class='btn-group'>
                    <button class='btn btn-primary btnEditar'><i class="bi bi-pencil-square"></i></button>
                    <button class='btn btn-danger btnBorrar'><i class="bi bi-trash"></i></button>
                </div></div>`,
                `<div class='text-center'><div class='btn-group'>
                    <button class='btn btn-info btnSeguimiento'><i class="bi bi-clipboard-check"></i></button>
                    <button class='btn btn-success btnCobro'><i class="bi bi-cash-coin"></i></button>
                    <button class='btn btn-secondary btnImprimir'><i class="bi bi-printer"></i></button>
                </div></div>`
            ];

            if (opcion === 1) {
                tablaPresupuestos.row.add(rowData).draw();
            } else {
                tablaPresupuestos.row(fila).data(rowData).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });


    // Agregar handler para el botón Visualizar
   document.addEventListener('click', async (e) => {
    if (e.target.closest('.btnVisualizar')) {
        const fila = e.target.closest("tr");
        const datos = tablaPresupuestos.row(fila).data();
        const id = parseInt(datos[0]);
        
        try {
            // Mostrar loader
            document.querySelector('#modalVisualizar .modal-body').innerHTML = `
                <div class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando presupuesto...</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.querySelector('#modalVisualizar'));
            modal.show();

            // Obtener datos del presupuesto
            const response = await fetch(`bd/obtener_presupuesto.php?id=${id}`);
            const resultado = await response.json();

            if (!resultado.success) {
                throw new Error(resultado.error || 'Error al cargar el presupuesto');
            }

            const presupuesto = resultado.data;

            // Llenar el modal con los datos
            document.querySelector('#modalVisualizar .modal-body').innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>Cliente:</strong></h6>
                        <p id="visualizar-cliente">${presupuesto.cliente}</p>
                    </div>
                    <div class="col-md-3">
                        <h6><strong>Fecha:</strong></h6>
                        <p id="visualizar-fecha">${presupuesto.fecha}</p>
                    </div>
                    <div class="col-md-3">
                        <h6><strong>Estado:</strong></h6>
                        <p id="visualizar-estado">${presupuesto.estado}</p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Lugar de Entrega:</strong></h6>
                    <p id="visualizar-lugar">${presupuesto.lugar}</p>
                </div>
                
                <h5 class="mt-4 mb-3">Productos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabla-productos">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-productos">
                            ${presupuesto.productos.map(p => `
                                <tr>
                                    <td>${p.nomb_producto || p.productos}</td>
                                    <td>$${parseFloat(p.precio_unitario).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6><strong>Observaciones:</strong></h6>
                        <p id="visualizar-observaciones">${presupuesto.observaciones}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5><strong>Mano de Obra: $<span id="visualizar-mano-obra">${parseFloat(presupuesto.mano_obra).toFixed(2)}</span></strong></h5>
                        <h4><strong>Total: $<span id="visualizar-total">${parseFloat(presupuesto.total).toFixed(2)}</span></strong></h4>
                    </div>
                </div>
            `;

        } catch (error) {
            console.error('Error:', error);
            document.querySelector('#modalVisualizar .modal-body').innerHTML = `
                <div class="alert alert-danger">
                    <h5>Error al cargar el presupuesto</h5>
                    <p>${error.message}</p>
                    <button class="btn btn-sm btn-secondary" onclick="location.reload()">Recargar</button>
                </div>
            `;
        }
    }
});

    // Resetear productos al abrir modal nuevo
    document.querySelector("#btnNuevo").addEventListener('click', () => {
    productosSeleccionados = [];
    document.querySelector("#contenedor-productos").innerHTML = '';
    });

    // Seguimiento button handler
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnSeguimiento')) {
            fila = e.target.closest("tr");
            const datos = tablaPresupuestos.row(fila).data();
            id = parseInt(datos[0]);
            
            // Aquí puedes cargar datos adicionales para el seguimiento
            document.querySelector("#modalSeguimiento .modal-title").textContent = `Seguimiento - ${datos[1]}`;
            
            // Cargar estado actual
            // Esto es un ejemplo, deberías implementar una llamada AJAX para obtener los datos reales
            const estadoActual = datos[4]; // "Completado", "En proceso", etc.
            document.querySelector("#estado_seguimiento").value = estadoActual;
            
            const modalSeguimiento = new bootstrap.Modal(document.querySelector("#modalSeguimiento"));
            modalSeguimiento.show();
        }
    });

    // Cobro button handler
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnCobro')) {
            fila = e.target.closest("tr");
            const datos = tablaPresupuestos.row(fila).data();
            alert(`Funcionalidad de cobro para el presupuesto ${datos[0]} - ${datos[1]}`);
            // Implementar lógica de cobro aquí
        }
    });

    // Imprimir button handler
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnImprimir')) {
            fila = e.target.closest("tr");
            const datos = tablaPresupuestos.row(fila).data();
            window.open(`imprimir_presupuesto.php?id=${datos[0]}`, '_blank');
        }
    });
});