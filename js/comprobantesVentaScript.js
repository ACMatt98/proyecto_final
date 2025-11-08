document.addEventListener('DOMContentLoaded', function() {
    let tablaComprobantes;
    
    // Inicializar DataTable
    tablaComprobantes = new DataTable("#tablaComprobantes", {
        columnDefs: [{
            targets: -1,
            data: null,
            defaultContent: `<div class='text-center'>
                <div class='btn-group'>
                    <button class='btn btn-danger btnBorrar'>
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class='btn btn-primary btnVisualizar'>
                        <i class="bi bi-eye"></i>
                    </button>
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
            }
        }
    });

    // Cargar lista de clientes al abrir el modal
    document.querySelector("#btnNuevo").addEventListener('click', async function() {
        try {
            const response = await fetch('bd/get_clientes.php');
            const clientes = await response.json();
            
            const selectClientes = document.querySelector("#cliente");
            selectClientes.innerHTML = '<option value="">Seleccione un cliente</option>';
            
            clientes.forEach(cliente => {
                selectClientes.innerHTML += `<option value="${cliente.id_cliente}">
                    ${cliente.nombre_cliente} ${cliente.apellido_cliente}
                </option>`;
            });
            
            const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
            modalCRUD.show();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar la lista de clientes');
        }
    });

    // Manejar envío del formulario
    document.querySelector("#formComprobantes").addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('n_comprob_vta', document.querySelector("#n_comprob_vta").value);
        formData.append('cliente', document.querySelector("#cliente").value);
        formData.append('fecha', document.querySelector("#fecha").value);
        formData.append('monto', document.querySelector("#monto").value);
        formData.append('tipo_factura', document.querySelector("#tipo_factura").value);
        formData.append('archivo', document.querySelector("#archivo").files[0]);
        
        // Añadimos la opción para que el backend sepa que es una CREACIÓN
        formData.append('opcion', 1);

        try {
            const response = await fetch('bd/crud_comprobantes_venta.php', {
                method: 'POST',
                body: formData
            });

            // Mejoramos el manejo de errores
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error en la red');
            }
            
            const result = await response.json();
            if(result.success) {
                alert(result.message);
                location.reload();
            } else {
                // Esto se ejecutará si el servidor responde con success: false
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al guardar el comprobante');
        }
    });

    // Visualizar comprobante
    document.addEventListener('click', async function(e) {
        if(e.target.closest('.btnVisualizar')) {
            const fila = e.target.closest("tr");
            const id = fila.cells[0].textContent;

            try {
                const response = await fetch(`bd/get_comprobante.php?id=${id}`);
                const data = await response.json();

                const visor = document.querySelector("#visorArchivo");
                if(data.archivo.endsWith('.pdf')) {
                    visor.innerHTML = `<embed src="${data.archivo}" width="100%" height="500px" type="application/pdf">`;
                } else {
                    visor.innerHTML = `<img src="${data.archivo}" class="img-fluid">`;
                }

                const modalVisualizador = new bootstrap.Modal(document.querySelector("#modalVisualizador"));
                modalVisualizador.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar el comprobante');
            }
        }
    });
    // Borrar comprobante
    document.addEventListener('click', async function(e) {
        if(e.target.closest('.btnBorrar')) {
            const fila = e.target.closest("tr");
            const id = fila.dataset.id; // Obtenemos el ID desde el atributo data-id
            const n_comprob = fila.cells[0].textContent;

            if(confirm(`¿Está seguro de eliminar el comprobante N° ${n_comprob}?`)) {
                try {
                    const formData = new FormData();
                    formData.append('opcion', 3); // Opción para borrar
                    formData.append('id', id);

                    const response = await fetch('bd/crud_comprobantes_venta.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) throw new Error('Error en la respuesta del servidor.');

                    const result = await response.json();

                    if(result.success) {
                        tablaComprobantes.row(fila).remove().draw();
                        alert(result.message);
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'No se pudo eliminar el comprobante.');
                }
            }
        }
    });
});