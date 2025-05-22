document.addEventListener('DOMContentLoaded', () => {
    let tablaProveedores;
    let fila;
    let id = null;
    let opcion;

    // Initialize DataTable
    tablaProveedores = new DataTable("#tablaProveedores", {
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
        document.querySelector("#formProveedores").reset();
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Proveedor";
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
            const nombre_proveedor = fila.cells[1].textContent;
            const direccion_proveedor = fila.cells[2].textContent;
            const telefono_proveedor = fila.cells[3].textContent;

            document.querySelector("#nombre_proveedor").value = nombre_proveedor;
            document.querySelector("#direccion_proveedor").value = direccion_proveedor;
            document.querySelector("#telefono_proveedor").value = telefono_proveedor;
            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Proveedor";
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
            
            if (confirm(`¿Está seguro de eliminar el proveedor: ${id}?`)) {
                try {
                    const response = await fetch('bd/crud_proveedor.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    tablaProveedores.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el proveedor');
                }
            }
        }
    });

    // Form submit handler
    document.querySelector("#formProveedores").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nombre_proveedor: document.querySelector("#nombre_proveedor").value.trim(),
            direccion_proveedor: document.querySelector("#direccion_proveedor").value.trim(),
            telefono_proveedor: document.querySelector("#telefono_proveedor").value.trim(),
            id,
            opcion
        };

        try {
            const response = await fetch('bd/crud_proveedor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const { id_proveedor: newId, nombre_proveedor, direccion_proveedor, telefono_proveedor } = data[0];

            if (opcion === 1) {
                tablaProveedores.row.add([newId, nombre_proveedor, direccion_proveedor, telefono_proveedor]).draw();
            } else {
                tablaProveedores.row(fila).data([newId, nombre_proveedor, direccion_proveedor, telefono_proveedor]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});