document.addEventListener('DOMContentLoaded', () => {
    let tablaClientes;
    let fila;
    let id = null;
    let opcion;

    
    // Initialize DataTable
    tablaClientes = new DataTable("#tablaClientes", {
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
        document.querySelector("#formClientes").reset();
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Cliente";
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
            const nombre_cliente = fila.cells[1].textContent;
            const apellido_cliente = fila.cells[2].textContent;
            const telefono_cliente = fila.cells[3].textContent;
            const direccion_cliente = fila.cells[4].textContent;

            document.querySelector("#nombre_cliente").value = nombre_cliente;
            document.querySelector("#apellido_cliente").value = apellido_cliente;
            document.querySelector("#telefono_cliente").value = telefono_cliente;
            document.querySelector("#direccion_cliente").value = direccion_cliente;
            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Cliente";
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
            
            if (confirm(`¿Está seguro de eliminar el cliente: ${id}?`)) {
                try {
                    const response = await fetch('bd/crud_.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    tablaClientes.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el cliente');
                }
            }
        }
    });

    // Form submit handler
    document.querySelector("#formClientes").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nombre_cliente: document.querySelector("#nombre_cliente").value.trim(),
            apellido_cliente: document.querySelector("#apellido_cliente").value.trim(),
            telefono_cliente: document.querySelector("#telefono_cliente").value.trim(),
            direccion_cliente: document.querySelector("#direccion_cliente").value.trim(),
            id,
            opcion
        };

        try {
            const response = await fetch('bd/crud_.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const { id_cliente: newId, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente } = data[0];

            if (opcion === 1) {
                tablaClientes.row.add([newId, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente]).draw();
            } else {
                tablaClientes.row(fila).data([newId, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});