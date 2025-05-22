document.addEventListener('DOMContentLoaded', () => {
    let tablaPresupuestos;
    let fila;
    let id = null;
    let opcion;

    // Initialize DataTable
    tablaPresupuestos = new DataTable("#tablaPresupuestos", {
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
        document.querySelector("#formPresupuestos").reset();
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Presupuesto";
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
            const lugar = fila.cells[3].textContent;
            const precio_total = parseFloat(fila.cells[4].textContent);
            const mano_obra = parseFloat(fila.cells[5].textContent);
            const estado = fila.cells[6].textContent;

            document.querySelector("#fecha_presup").value = fecha;
            document.querySelector("#lugar").value = lugar;
            document.querySelector("#precio_total_presup").value = precio_total;
            document.querySelector("#mano_obra").value = mano_obra;
            document.querySelector("#id_estado_presupuesto").value = estado;
            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Presupuesto";
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
            
            if (confirm(`¿Está seguro de eliminar el presupuesto: ${id}?`)) {
                try {
                    const response = await fetch('bd/crud_presupuestos.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
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
        
        const formData = {
            fecha_presup: document.querySelector("#fecha_presup").value.trim(),
            lugar: document.querySelector("#lugar").value.trim(),
            precio_total_presup: document.querySelector("#precio_total_presup").value.trim(),
            mano_obra: document.querySelector("#mano_obra").value.trim(),
            id_estado_presupuesto: document.querySelector("#id_estado_presupuesto").value.trim(),
            id_cliente: document.querySelector("#id_cliente").value.trim(),
            id,
            opcion
        };

        try {
            const response = await fetch('bd/crud_presupuestos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const { id_presupuesto: newId, fecha_presup, lugar, precio_total_presup, 
                    mano_obra, estado, nombre_cliente, apellido_cliente } = data[0];
            const cliente = nombre_cliente + ' ' + apellido_cliente;

            if (opcion === 1) {
                tablaPresupuestos.row.add([newId, fecha_presup, cliente, lugar, precio_total_presup, mano_obra, estado]).draw();
            } else {
                tablaPresupuestos.row(fila).data([newId, fecha_presup, cliente, lugar, precio_total_presup, mano_obra, estado]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});