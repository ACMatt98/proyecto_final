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
        
        // Se añade la opción  CREACIÓN
        formData.append('opcion', 1);

        try {
            const response = await fetch('bd/crud_comprobantes_venta.php', {
                method: 'POST',
                body: formData
            });

            //manejo de errores
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error en la red');
            }
            
            const result = await response.json();
            if(result.success) {
                alert(result.message);
                location.reload();
            } else {
                // Esto se ejecuta si el servidor responde con success: false
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
            // 1. CAPTURAr AMBOS IDs
            const idInterno = fila.dataset.id;        // Para Imprimir (ID único de la BD)
            const nComprobante = fila.cells[0].textContent; // Para Visualizar (Número que se ve en la tabla)

            // 2. ASIGNAMOS EL ID INTERNO AL BOTÓN DE IMPRIMIR
            // Esto asegura que al imprimir busque el registro exacto en la BD
            const btnImprimir = document.querySelector("#btnImprimirComprobante");
            if(btnImprimir) btnImprimir.dataset.id = idInterno;

            // Referencias al modal y visor
            const visor = document.querySelector("#visorArchivo");
            const modalElement = document.querySelector("#modalVisualizador");
            const modalTitle = modalElement.querySelector(".modal-title");

            // Limpiar visor antes de cargar
            visor.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p>Cargando comprobante...</p></div>';
            
            const modalVisualizador = new bootstrap.Modal(modalElement);
            modalVisualizador.show();

            try {

                const response = await fetch(`bd/get_comprobante.php?id=${nComprobante}`);

                if (!response.ok) {
                    throw new Error('Error al conectar con el servidor');
                }

                const data = await response.json();

                if(!data.success) {
                    visor.innerHTML = `<div class="alert alert-warning text-center">${data.message || 'No se encontró información.'}</div>`;
                    return;
                }

                // CASO A: Es un archivo (Imagen o PDF)
                if (data.tipo === 'archivo') {
                    modalTitle.textContent = "Visualizar Comprobante (Archivo)";
                    if(data.contenido.endsWith('.pdf')) {
                        visor.innerHTML = `<embed src="${data.contenido}" width="100%" height="500px" type="application/pdf">`;
                    } else {
                        visor.innerHTML = `<img src="${data.contenido}" class="img-fluid" alt="Comprobante">`;
                    }
                } 
                // CASO B: Es un Ticket Generado (HTML)
                else if (data.tipo === 'html') {
                    modalTitle.textContent = "Ticket de Pago Digital";
                    // Insertamos el HTML del ticket directamente
                    visor.innerHTML = data.contenido;
                }

            } catch (error) {
                console.error('Error:', error);
                visor.innerHTML = `<div class="alert alert-danger">Error al cargar el comprobante: ${error.message}</div>`;
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

    // Boton imprimir dentro del modal de visualización
    document.addEventListener('click', function(e) {
        // Verificamos si el clic fue en el botón de imprimir (o en su icono)
        const btn = e.target.closest('#btnImprimirComprobante');
        
        if (btn) {
            // Recuperamos el ID que guardamos al abrir el modal
            const idParaImprimir = btn.dataset.id;
            
            if (idParaImprimir) {
                // se abre generador de ticket en una pestaña nueva
                window.open(`bd/imprimir_ticket.php?id=${idParaImprimir}`, '_blank');
            } else {
                alert("Error: No se ha cargado un comprobante.");
            }
        }
    });
});