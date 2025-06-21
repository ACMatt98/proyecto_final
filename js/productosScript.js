document.addEventListener('DOMContentLoaded', () => {
    let tablaProductos;
    let fila;
    let id = null;
    let opcion;

    tablaProductos = new DataTable("#tablaProductos", {
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
            }
        }
    });

    document.querySelector("#btnNuevo").addEventListener('click', () => {
        document.querySelector("#formProductos").reset();
        const modalHeader = document.querySelector(".modal-header");
        modalHeader.style.backgroundColor = "#28a745";
        modalHeader.style.color = "white";
        document.querySelector(".modal-title").textContent = "Nuevo Producto";
        const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
        modalCRUD.show();
        id = null;
        opcion = 1;
    });

    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEditar')) {
            fila = e.target.closest("tr");
            id = parseInt(fila.cells[0].textContent);
            const nomb_producto = fila.cells[1].textContent;
            const precio_venta = fila.cells[2].textContent;
            const costo_produccion = fila.cells[3].textContent;
            const stock = fila.cells[4].textContent;

            document.querySelector("#nomb_producto").value = nomb_producto;
            document.querySelector("#precio_venta").value = precio_venta;
            document.querySelector("#costo_produccion").value = costo_produccion;
            document.querySelector("#stock").value = stock;

            opcion = 2;
            const modalHeader = document.querySelector(".modal-header");
            modalHeader.style.backgroundColor = "#007bff";
            modalHeader.style.color = "white";
            document.querySelector(".modal-title").textContent = "Editar Producto";
            const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
            modalCRUD.show();
        }
    });

    document.addEventListener('click', async (e) => {
        if (e.target.matches('.btnBorrar')) {
            fila = e.target.closest("tr");
            id = parseInt(fila.cells[0].textContent);
            const nombreProducto = fila.cells[1].textContent;
            opcion = 3;
            
            if (confirm(`¿Está seguro de eliminar el producto: ${nombreProducto}?`)) {
                try {
                    const response = await fetch('bd/crud_productos.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ opcion, id })
                    });
                    
                    if (!response.ok) throw new Error('Error en la red');
                    tablaProductos.row(fila).remove().draw();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el producto');
                }
            }
        }
    });

    document.querySelector("#formProductos").addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nomb_producto: document.querySelector("#nomb_producto").value.trim(),
            precio_venta: parseFloat(document.querySelector("#precio_venta").value),
            costo_produccion: parseFloat(document.querySelector("#costo_produccion").value),
            stock: parseInt(document.querySelector("#stock").value),
            id,
            opcion
        };

        try {
            const response = await fetch('bd/crud_productos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error('Error en la red');
            
            const data = await response.json();
            const { id_producto_term, nomb_producto, precio_venta, costo_produccion, stock } = data[0];

            if (opcion === 1) {
                tablaProductos.row.add([id_producto_term, nomb_producto, precio_venta, costo_produccion, stock]).draw();
            } else {
                tablaProductos.row(fila).data([id_producto_term, nomb_producto, precio_venta, costo_produccion, stock]).draw();
            }

            const modalCRUD = bootstrap.Modal.getInstance(document.querySelector("#modalCRUD"));
            modalCRUD.hide();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos');
        }
    });
});