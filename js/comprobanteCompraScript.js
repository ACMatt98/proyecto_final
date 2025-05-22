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

    // Nuevo button handler
    document.querySelector("#btnNuevo").addEventListener('click', () => {
        document.querySelector("#formComprobantes").reset();
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Comprobante";
        const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
        modalCRUD.show();
        id = null;
        opcion = 1; // alta
    });

    // Edit button handler
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEditar')) {
            fila = e.target.closest("tr");
            id = parseInt(fila.cells[0].textContent);
            const fecha = fila.cells[1].textContent;
            const n_de_comprob = fila.cells[2].textContent;
            const precio_total = fila.cells[3].textContent;
            const proveedor = fila.cells[4].textContent;

            document.querySelector("#fecha").value = fecha;
            document.querySelector("#n_de_comprob").value = n_de_comprob;
            document.querySelector("#precio_total").value = precio_total;
            // Find and select the correct provider in the dropdown
            const proveedorSelect = document.querySelector("#id_proveedor");
            Array.from(proveedorSelect.options).forEach(option => {
                if (option.text === proveedor) {
                    option.selected = true;
                }
            });
            
            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Comprobante";
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
            
            if (confirm(`¿Está seguro de eliminar el comprobante: ${id}?`)) {
                try {
                    const response = await fetch('bd/crud_comprobante_compra.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    tablaComprobantes.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el comprobante');
                }
            }
        }
    });

    // Form submit handler
    document.querySelector("#formComprobantes").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            fecha: document.querySelector("#fecha").value,
            n_de_comprob: document.querySelector("#n_de_comprob").value,
            precio_total: document.querySelector("#precio_total").value,
            id_proveedor: document.querySelector("#id_proveedor").value,
            id,
            opcion
        };

        try {
            const response = await fetch('bd/crud_comprobante_compra.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const { id_compro_comp: newId, fecha, n_de_comprob, precio_total, nombre_proveedor } = data[0];

            if (opcion === 1) {
                tablaComprobantes.row.add([newId, fecha, n_de_comprob, precio_total, nombre_proveedor]).draw();
            } else {
                tablaComprobantes.row(fila).data([newId, fecha, n_de_comprob, precio_total, nombre_proveedor]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});