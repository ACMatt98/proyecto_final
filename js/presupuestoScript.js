let productosSeleccionados = [];

document.addEventListener('DOMContentLoaded', () => {
    let tablaPresupuestos;
    let fila;
    let id = null;
    let opcion;

    // Inicialización de la tabla de datos
    tablaPresupuestos = new DataTable("#tablaPresupuestos", {
        columnDefs: [{
            targets: 0,
            visible: false
        }],
        responsive: true,
        language: {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "sProcessing": "Procesando...",
        }
    });

    // --- Funciones Auxiliares ---
    function calcularTotal() {
        let subtotal = productosSeleccionados.reduce((sum, producto) => sum + parseFloat(producto.precio), 0);
        let manoObra = parseFloat(document.querySelector("#mano_obra").value) || 0;
        document.querySelector("#precio_total_presup").value = (subtotal + manoObra).toFixed(2);
    }

    function renderizarProductosSeleccionados() {
        const contenedor = document.querySelector("#contenedor-productos");
        contenedor.innerHTML = '';
        productosSeleccionados.forEach(producto => {
            const productoTexto = `${producto.nombre} - $${parseFloat(producto.precio).toFixed(2)}`;
            const productoElement = document.createElement('div');
            productoElement.className = 'd-flex justify-content-between align-items-center mb-2';
            productoElement.innerHTML = `
                <span>${productoTexto}</span>
                <button type="button" class="btn btn-sm btn-danger btn-eliminar-producto" data-id="${producto.id}">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            contenedor.appendChild(productoElement);
        });
        calcularTotal();
    }

    // --- Event Listeners para el Modal Principal (CRUD) ---
    document.querySelector("#btn-agregar-producto").addEventListener('click', () => {
        const select = document.querySelector("#select-productos");
        const productoId = select.value;
        if (!productoId) return;

        const productoTexto = select.options[select.selectedIndex].text;
        const productoPrecio = parseFloat(select.options[select.selectedIndex].dataset.precio);

        if (!productosSeleccionados.some(p => p.id === productoId)) {
            productosSeleccionados.push({
                id: productoId,
                nombre: productoTexto.split(' - ')[0],
                precio: productoPrecio
            });
            renderizarProductosSeleccionados();
        }
    });

    document.querySelector("#mano_obra").addEventListener('input', calcularTotal);

    document.querySelector("#btnNuevo").addEventListener('click', () => {
        opcion = 1;
        id = null;
        productosSeleccionados = [];
        document.querySelector("#formPresupuestos").reset();
        renderizarProductosSeleccionados();
        document.querySelector("#modalTitle").textContent = "Nuevo Presupuesto";
        const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
        modalCRUD.show();
    });

    // --- DELEGACIÓN DE EVENTOS: Un solo listener para todos los clics ---
    document.addEventListener('click', async (e) => {
        const target = e.target;

        // --- Lógica de la tabla principal ---
        if (target.closest('.btnEditar')) {
            opcion = 2;
            fila = target.closest("tr");
            id = parseInt(tablaPresupuestos.row(fila).data()[0]);

            try {
                const response = await fetch(`bd/obtener_presupuesto.php?id=${id}`);
                const resultado = await response.json();
                if (!resultado.success) throw new Error(resultado.error);
                
                const presupuesto = resultado.data;
                
                document.querySelector("#fecha_presup").value = presupuesto.fecha_presup;
                document.querySelector("#lugar").value = presupuesto.lugar;
                document.querySelector("#precio_total_presup").value = presupuesto.precio_total_presup;
                document.querySelector("#mano_obra").value = presupuesto.mano_obra;
                document.querySelector("#id_estado_presupuesto").value = presupuesto.id_estado_presupuesto;
                document.querySelector("#id_cliente").value = presupuesto.id_cliente;
                document.querySelector("#observaciones").value = presupuesto.observaciones;

                productosSeleccionados = presupuesto.productos.map(p => ({
                    id: p.id_producto_term,
                    nombre: p.nomb_producto || p.productos,
                    precio: parseFloat(p.precio_unitario)
                }));
                renderizarProductosSeleccionados();

                document.querySelector("#modalTitle").textContent = "Editar Presupuesto";
                const modalCRUD = new bootstrap.Modal(document.querySelector("#modalCRUD"));
                modalCRUD.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los datos para editar.');
            }
        }
        else if (target.closest('.btnVisualizar')) {
            fila = target.closest("tr");
            id = parseInt(tablaPresupuestos.row(fila).data()[0]);

            try {
                const response = await fetch(`bd/obtener_presupuesto.php?id=${id}`);
                const resultado = await response.json();
                if (!resultado.success) throw new Error(resultado.error);
                
                const presupuesto = resultado.data;

                document.querySelector("#visualizar-cliente").textContent = presupuesto.cliente_completo;
                document.querySelector("#visualizar-fecha").textContent = presupuesto.fecha_presup;
                document.querySelector("#visualizar-estado").textContent = presupuesto.estado;
                document.querySelector("#visualizar-lugar").textContent = presupuesto.lugar;
                document.querySelector("#visualizar-observaciones").textContent = presupuesto.observaciones;
                document.querySelector("#visualizar-mano-obra").textContent = parseFloat(presupuesto.mano_obra).toFixed(2);
                document.querySelector("#visualizar-total").textContent = parseFloat(presupuesto.precio_total_presup).toFixed(2);

                const cuerpoTabla = document.querySelector("#cuerpo-tabla-productos");
                cuerpoTabla.innerHTML = '';
                presupuesto.productos.forEach(p => {
                    cuerpoTabla.innerHTML += `<tr><td>${p.productos}</td><td>$${parseFloat(p.precio_unitario).toFixed(2)}</td></tr>`;
                });

                const modalVisualizar = new bootstrap.Modal(document.querySelector("#modalVisualizar"));
                modalVisualizar.show();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar los datos para visualizar.');
            }
        }
        else if (target.closest('.btnBorrar')) {
            fila = target.closest("tr");
            const datos = tablaPresupuestos.row(fila).data();
            id = parseInt(datos[0]);
            
            if (confirm(`¿Está seguro de eliminar el presupuesto para ${datos[1]}?`)) {
                try {
                    const formData = new FormData();
                    formData.append('opcion', 3);
                    formData.append('id', id);

                    const response = await fetch('bd/crud_presupuestos.php', { method: 'POST', body: formData });
                    const result = await response.json();

                    if (!result.success) throw new Error(result.message);
                    
                    tablaPresupuestos.row(fila).remove().draw();
                    alert(result.message);
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'Error al eliminar el presupuesto');
                }
            }
        }
        else if (target.closest('.btnSeguimiento')) {
            fila = target.closest("tr");
            id = parseInt(tablaPresupuestos.row(fila).data()[0]);
            
            try {
                const response = await fetch(`bd/obtener_seguimiento.php?id=${id}`);
                const result = await response.json();
                if (!result.success) throw new Error(result.message);

                const { presupuesto, todos_los_estados, materiales } = result.data;

                document.querySelector("#seguimiento_id_presupuesto").value = id;
                document.querySelector("#estado-actual-texto").textContent = presupuesto.estado;
                document.querySelector("#seguimiento_observaciones").value = presupuesto.observaciones;

                const contenedorRadios = document.querySelector("#contenedor-estados-radio");
                contenedorRadios.innerHTML = '';
                todos_los_estados.forEach(estado => {
                    const isChecked = estado.id_estado == presupuesto.id_estado_presupuesto ? 'checked' : '';
                    contenedorRadios.innerHTML += `<div class="form-check"><input class="form-check-input" type="radio" name="nuevo_estado" id="estado-${estado.id_estado}" value="${estado.id_estado}" ${isChecked}><label class="form-check-label" for="estado-${estado.id_estado}">${estado.estado}</label></div>`;
                });

                const listaMateriales = document.querySelector("#lista-materiales-requeridos");
                const btnDescontar = document.querySelector("#btn-descontar-stock");
                listaMateriales.innerHTML = '';
                btnDescontar.disabled = true;

                if (materiales.length > 0) {
                    materiales.forEach(mat => {
                        listaMateriales.innerHTML += `<li class="list-group-item">${mat.nombre_material} (${mat.cantidad_total} ${mat.unidad_medida})</li>`;
                    });
                    btnDescontar.disabled = false;
                } else {
                    listaMateriales.innerHTML = '<li class="list-group-item">No se requieren materiales.</li>';
                }

                const modalSeguimiento = new bootstrap.Modal(document.querySelector("#modalSeguimiento"));
                modalSeguimiento.show();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al cargar los datos de seguimiento.');
            }
        }
        else if (target.closest('.btnCobro')) {
            fila = target.closest("tr");
            id = parseInt(tablaPresupuestos.row(fila).data()[0]);
            

            try {
                const response = await fetch(`bd/obtener_cobro.php?id=${id}`);
                const result = await response.json();
                if (!result.success) throw new Error(result.message);

                const datos = result.data;
                const total = parseFloat(datos.precio_total_presup);
                const seña = parseFloat(datos.seña) || 0;
                const pagoFinal = parseFloat(datos.pago_final) || 0;
                const pendiente = total - seña - pagoFinal;

                document.querySelector("#cobro-saldo-total").textContent = `$${total.toFixed(2)}`;
                document.querySelector("#cobro-seña").textContent = `$${seña.toFixed(2)}`;
                document.querySelector("#cobro-pagos").textContent = `$${pagoFinal.toFixed(2)}`;
                document.querySelector("#cobro-saldo-pendiente").textContent = `$${pendiente.toFixed(2)}`;

                document.querySelector("#cobro_id_presupuesto").value = id;

                const inputSeña = document.querySelector("#cobro-input-seña");
                const btnRegistrarSeña = document.querySelector("#btn-registrar-seña");
                const btnRegistrarPago = document.querySelector("#btn-registrar-pago");
                const btnImprimirRecibo = document.querySelector("#btn-imprimir-recibo");

                if (seña > 0) {
                    inputSeña.disabled = true;
                    btnRegistrarSeña.disabled = true;
                    inputSeña.value = '';
                    inputSeña.placeholder = "Seña ya registrada";
                } else {
                    inputSeña.disabled = false;
                    btnRegistrarSeña.disabled = false;
                    inputSeña.value = '';
                    inputSeña.placeholder = "Monto de la seña";
                }

                btnRegistrarPago.disabled = (pendiente <= 0);
                btnImprimirRecibo.disabled = (pendiente > 0);

                const modalCobro = new bootstrap.Modal(document.querySelector("#modalCobro"));
                modalCobro.show();
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al cargar los datos de cobro.');
            }
        }
        else if (target.matches('.btnImprimir')) {
            fila = target.closest("tr");
            id = parseInt(tablaPresupuestos.row(fila).data()[0]);
            window.open(`imprimir_presupuesto.php?id=${id}`, '_blank');
        }

        // --- Lógica de los botones DENTRO de los modales ---
        else if (target.closest('.btn-eliminar-producto')) {
            const productoId = target.closest('.btn-eliminar-producto').dataset.id;
            productosSeleccionados = productosSeleccionados.filter(p => p.id !== productoId);
            renderizarProductosSeleccionados();
        }
        else if (target.closest('#btn-registrar-seña')) {
            const idPresupuesto = document.querySelector("#cobro_id_presupuesto").value;
            const monto = document.querySelector("#cobro-input-seña").value;

            if (!monto || parseFloat(monto) <= 0) {
                alert("Por favor, ingrese un monto válido para la seña.");
                return;
            }

            const formData = new FormData();
            formData.append('opcion', 6);
            formData.append('id', idPresupuesto);
            formData.append('monto', monto);

            try {
                const response = await fetch('bd/crud_presupuestos.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (!result.success) throw new Error(result.message);

                alert(result.message);
                location.reload();
            } catch (error) {
                alert(error.message || "Error al registrar la seña.");
            }
        }
        else if (target.closest('#btn-registrar-pago')) {
            const idPresupuesto = document.querySelector("#cobro_id_presupuesto").value;
            const saldoPendienteTexto = document.querySelector("#cobro-saldo-pendiente").textContent;
            const saldoPendiente = parseFloat(saldoPendienteTexto.replace('$', ''));

            const montoPagado = prompt(`El saldo pendiente es de $${saldoPendiente.toFixed(2)}. Ingrese el monto a pagar:`, saldoPendiente.toFixed(2));

            if (montoPagado === null || montoPagado === "" || parseFloat(montoPagado) <= 0 || parseFloat(montoPagado) > saldoPendiente) {
                alert("Monto inválido. No puede ser mayor al saldo pendiente.");
                return;
            }

            const formData = new FormData();
            formData.append('opcion', 7);
            formData.append('id', idPresupuesto);
            formData.append('monto', montoPagado);

            try {
                const response = await fetch('bd/crud_presupuestos.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (!result.success) throw new Error(result.message);

                alert(result.message);
                location.reload();
            } catch (error) {
                alert(error.message || "Error al registrar el pago.");
            }
        }
        else if (target.closest('#btn-descontar-stock')) {
            const idPresupuesto = document.querySelector("#seguimiento_id_presupuesto").value;
            if (!confirm("¿Está seguro? Esta acción descontará los materiales del inventario y no se puede revertir fácilmente.")) return;

            const formData = new FormData();
            formData.append('opcion', 5);
            formData.append('id', idPresupuesto);

            try {
                const response = await fetch('bd/crud_presupuestos.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (!result.success) throw new Error(result.message);

                alert(result.message);
                target.disabled = true;
                target.textContent = "Stock Descontado";
            } catch (error) {
                alert(error.message || 'Ocurrió un error al descontar el stock.');
            }
        }
         else if (target.closest('#btn-imprimir-recibo')) {
            window.open('img/Presupuesto_impreso.png', '_blank');
        }

    });

    // --- Listeners para los formularios (evento SUBMIT) ---
    document.querySelector("#formPresupuestos").addEventListener('submit', async (e) => {
        e.preventDefault();
        const id_cliente = document.querySelector("#id_cliente").value;
        if (!id_cliente) {
            alert("Error: Por favor, seleccione un cliente.");
            return;
        }
        
        const formData = new FormData(document.querySelector("#formPresupuestos"));
        formData.append('productos', JSON.stringify(productosSeleccionados));
        formData.append('opcion', opcion);
        if (id) formData.append('id', id);

        try {
            const response = await fetch('bd/crud_presupuestos.php', { method: 'POST', body: formData });
            if (!response.ok) {
                 const errorData = await response.json();
                 throw new Error(errorData.message || 'Error del servidor');
            }
            const result = await response.json();
            if (!result.success) throw new Error(result.message);
            
            alert(result.message);
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al guardar los datos');
        }
    });

    document.querySelector("#formSeguimiento").addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(document.querySelector("#formSeguimiento"));
        const idPresupuesto = formData.get('id_presupuesto');
        const nuevoEstado = formData.get('nuevo_estado');

        if (!nuevoEstado) {
            alert("Por favor, seleccione un nuevo estado.");
            return;
        }

        try {
            const updateData = new FormData();
            updateData.append('opcion', 4);
            updateData.append('id', idPresupuesto);
            updateData.append('id_estado_presupuesto', nuevoEstado);

            const response = await fetch('bd/crud_presupuestos.php', { method: 'POST', body: updateData });
            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            alert(result.message);
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Error al actualizar el estado.');
        }
    });
});