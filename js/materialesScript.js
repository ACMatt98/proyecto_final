document.addEventListener('DOMContentLoaded', () => {
    let tablaMateriales;
    let fila;
    let id = null;
    let opcion;

    // Initialize DataTable
    tablaMateriales = new DataTable("#tablaMateriales", {
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
        document.querySelector("#formMateriales").reset();
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Material";
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
            const nombre_material = fila.cells[1].textContent;
            const existencia = fila.cells[2].textContent;
            const marca = fila.cells[3].textContent;

            document.querySelector("#nombre_material").value = nombre_material;
            document.querySelector("#existencia").value = existencia;
            document.querySelector("#marca").value = marca;
            opcion = 2; // editar

            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Material";
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
            
            if (confirm(`¿Está seguro de eliminar el material: ${id}?`)) {
                try {
                    const response = await fetch('bd/crud_materiales.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    tablaMateriales.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el material');
                }
            }
        }
    });

    // Form submit handler
    document.querySelector("#formMateriales").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nombre_material: document.querySelector("#nombre_material").value.trim(),
            existencia: parseInt(document.querySelector("#existencia").value.trim()),
            marca: document.querySelector("#marca").value.trim(),
            id,
            opcion
        };

        try {
            const response = await fetch('bd/crud_materiales.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            const { id_materiales: newId, nombre_material, existencia, marca } = data[0];

            if (opcion === 1) {
                tablaMateriales.row.add([newId, nombre_material, existencia, marca]).draw();
            } else {
                tablaMateriales.row(fila).data([newId, nombre_material, existencia, marca]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});