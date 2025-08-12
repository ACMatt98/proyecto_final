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


    // Template para fila de material
    const templateMaterial = `
    <tr>
        <td>
            <select class="form-control material-select" required>
                <option value="">Seleccione material</option>
                <?php
                $consulta = "SELECT id_materiales, nombre_material FROM materiales";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $materiales = $resultado->fetchAll(PDO::FETCH_ASSOC);
                foreach($materiales as $material) {
                    echo "<option value='".$material['id_materiales']."'>".$material['nombre_material']."</option>";
                }
                ?>
            </select>
        </td>
        <td><input type="number" step="0.01" class="form-control cantidad-input" required></td>
        <td><input type="number" step="0.01" class="form-control precio-input" required></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btnEliminarMaterial">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;

    // Agregar nuevo material usando template
    document.querySelector('#btnAgregarMaterial').addEventListener('click', () => {
        const template = document.querySelector('#templateMaterial');
        const tbody = document.querySelector('#materialesCompraBody');
        const clone = template.content.cloneNode(true);
        tbody.appendChild(clone);
    });

    // Eliminar material
    document.addEventListener('click', (e) => {
        if (e.target.matches('.btnEliminarMaterial') || e.target.closest('.btnEliminarMaterial')) {
            const button = e.target.closest('.btnEliminarMaterial');
            button.closest('tr').remove();
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
        const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
        modalCRUD.show();
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

            // Cargar materiales existentes
            try {
                const response = await fetch('bd/get_materiales_comprobante.php', {
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
                    const inputPrecio = clone.querySelector('.precio-input');
                    
                    Array.from(selectMaterial.options).forEach(option => {
                        if (option.text === material.nombre_material) option.selected = true;
                    });
                    
                    inputCantidad.value = material.cantidad;
                    inputPrecio.value = material.precio_unitario;
                    
                    tbody.appendChild(clone);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los materiales del comprobante');
            }

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
    // Eliminar material
    document.addEventListener('click', (e) => {
        if (e.target.closest('.btnEliminarMaterial')) {
            e.target.closest('tr').remove();
        }
    });

    // Form submit handler con validación mejorada
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

        // Recolectar materiales con validación
        const materiales = [];
        let materialesValidos = true;
        
        document.querySelectorAll("#materialesCompraBody tr").forEach(row => {
            const id_materiales = row.querySelector('.material-select').value;
            const cantidad = row.querySelector('.cantidad-input').value;
            const precio_unitario = row.querySelector('.precio-input').value;
            
            if (!id_materiales || !cantidad || !precio_unitario) {
                materialesValidos = false;
                return;
            }
            
            materiales.push({
                id_materiales,
                cantidad: parseFloat(cantidad),
                precio_unitario: parseFloat(precio_unitario)
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



