// Variables Globales
window.currentLoteId = null;
window.currentModeloId = null;
window.currentModemId = null; 
window.lotes = [];
window.modelos = [];
window.templates = [];
window.inventario = [];
window.selectedModems = new Set();
window.filtroEstadoInv = 'TODOS';

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    fetchLotes();
    fetchModelos();
    fetchTemplates();
    fetchInventario();
    switchView('dashboard');
});

// --- LOTES Y DASHBOARD ---

async function fetchLotes() {
    try {
        const res = await fetch('api.php?controller=Lotes&action=listar');
        const data = await res.json();
        if (data.status === 'success') {
            window.lotes = data.data;
            document.getElementById('stat-lotes').textContent = window.lotes.length;
            renderLotesDashboard();
        }
    } catch (e) { console.error("Error cargando lotes:", e); }
}

function formatDate(dateStr) {
    if (!dateStr) return '---';
    const date = new Date(dateStr);
    return date.toLocaleDateString('es-MX', { 
        day: '2-digit', 
        month: 'short', 
        year: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function renderLotesDashboard(filtered = null) {
    const container = document.getElementById('lotes-container');
    if (!container) return;
    const items = filtered || window.lotes;
    container.innerHTML = items.map(l => {
        let dateInfo = `<div class="flex flex-col gap-1">
            <span class="text-[9px] text-slate-400 font-bold uppercase">Creado: ${formatDate(l.fecha_creacion)}</span>`;
        
        if (l.estado === 'COMPLETADO' || l.estado === 'IMPRESO') {
            dateInfo += `<span class="text-[9px] text-green-500 font-black uppercase italic">Finalizado: ${formatDate(l.fecha_finalizacion)}</span>`;
        }
        dateInfo += `</div>`;

        return `
        <div onclick="seleccionarLote(${l.id})" class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm hover:shadow-2xl transition-all cursor-pointer group">
            <div class="h-16 w-16 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all mb-8 shadow-inner">
                <i class="fas fa-barcode text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 italic tracking-tighter uppercase mb-2">${l.nombre}</h3>
            <div class="flex flex-col gap-4 mt-6">
                <div class="flex justify-between items-center">
                    <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest ${
                        l.estado === 'NUEVO' ? 'bg-blue-50 text-blue-600' : 
                        l.estado === 'REVISION' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600'
                    }">${l.estado}</span>
                </div>
                ${dateInfo}
            </div>
        </div>
    `}).join('');
}

function filtrarLotes(query) {
    const q = query.toLowerCase();
    const filtered = window.lotes.filter(l => l.nombre.toLowerCase().includes(q));
    renderLotesDashboard(filtered);
}

async function seleccionarLote(id) {
    window.currentLoteId = id;
    const lote = window.lotes.find(l => l.id == id);
    
    if (lote.estado === 'REVISION' || lote.estado === 'IMPRESO') {
        switchView('impresion');
        renderImpresion(lote);
    } else {
        switchView('produccion');
        document.getElementById('nav-produccion').classList.remove('hidden');
        document.getElementById('prod-lote-name').textContent = lote.nombre;
        document.getElementById('prod-lote-desc').textContent = lote.descripcion || 'Sin descripción';
        renderBotonesAccion(lote.estado);
        document.getElementById('prod-form-card').style.display = (lote.estado === 'NUEVO') ? 'block' : 'none';
        fetchModemsLote();
    }
}

async function cambiarEstadoLote(nuevoEstado) {
    try {
        const res = await fetch('api.php?controller=Lotes&action=cambiarEstado', {
            method: 'POST',
            body: JSON.stringify({ lote_id: window.currentLoteId, nuevo_estado: nuevoEstado })
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast(`Lote actualizado a ${nuevoEstado}`, 'success');
            await fetchLotes();
            seleccionarLote(window.currentLoteId);
        }
    } catch (e) { showToast('Error al actualizar estado', 'error'); }
}

async function regresarEstadoLote() {
    if (!window.currentLoteId) return;
    const lote = window.lotes.find(l => l.id == window.currentLoteId);
    let anterior = 'NUEVO';
    
    if (lote.estado === 'IMPRESO') anterior = 'REVISION';
    if (lote.estado === 'COMPLETADO') anterior = 'IMPRESO';
    
    abrirConfirm('Regresar Lote', `¿Deseas regresar este lote al estado de ${anterior}?`, () => {
        cambiarEstadoLote(anterior);
        cerrarConfirm();
    });
}

function renderBotonesAccion(estado) {
    const container = document.getElementById('lote-dynamic-actions');
    if (!container) return;
    if (estado === 'NUEVO') container.innerHTML = `<button onclick="cambiarEstadoLote('REVISION')" class="bg-white/10 text-white px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-white/20 transition-all">Enviar a Impresión</button>`;
    else if (estado === 'REVISION') container.innerHTML = `<button onclick="cambiarEstadoLote('IMPRESO')" class="bg-white text-blue-600 px-8 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-xl">Marcar Impreso</button>`;
    else if (estado === 'IMPRESO') container.innerHTML = `<button onclick="cambiarEstadoLote('COMPLETADO')" class="bg-green-500 text-white px-8 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest">Finalizar</button>`;
    else container.innerHTML = '';
}

// --- INVENTARIO GLOBAL ---

async function fetchInventario() {
    try {
        const res = await fetch('api.php?controller=Modems&action=listar');
        const data = await res.json();
        if (data.status === 'success') {
            window.inventario = data.data;
            document.getElementById('stat-modems').textContent = window.inventario.length;
            renderInventario();
        }
    } catch (e) { console.error("Error cargando inventario:", e); }
}

function renderInventario(items = null) {
    const container = document.getElementById('inventario-table-body');
    if (!container) return;
    
    let list = items || window.inventario;
    if (window.filtroEstadoInv !== 'TODOS') {
        list = list.filter(m => m.estado_inventario === window.filtroEstadoInv);
    }

    container.innerHTML = list.map(m => `
        <tr class="hover:bg-slate-50 transition-all group ${window.selectedModems.has(m.id) ? 'bg-indigo-50/50' : ''}">
            <td class="px-8 py-6">
                <input type="checkbox" onchange="toggleSelectModem(${m.id}, this.checked)" ${window.selectedModems.has(m.id) ? 'checked' : ''} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            </td>
            <td class="px-4 py-6">
                <div class="flex flex-col">
                    <span class="text-sm font-mono font-bold text-slate-800">${m.sn}</span>
                    <span class="text-[8px] text-slate-400 uppercase font-black">Registrado: ${formatDate(m.fecha_registro)}</span>
                </div>
            </td>
            <td class="px-8 py-6 text-xs text-slate-500 font-black uppercase italic">${m.modelo_nombre || '---'}</td>
            <td class="px-8 py-6">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-tighter">${m.ssid}</span>
                    <span class="text-[10px] font-mono text-slate-400 mt-0.5">${m.password}</span>
                </div>
            </td>
            <td class="px-8 py-6">
                <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest ${
                    m.estado_inventario === 'BODEGA' ? 'bg-blue-50 text-blue-600' : 
                    m.estado_inventario === 'PRESTADO' ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600'
                }">${m.estado_inventario}</span>
            </td>
            <td class="px-8 py-6 text-right">
                <div class="flex justify-end gap-2">
                    <button onclick="abrirModalAsignarLote(${m.id})" class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-400 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center" title="Asignar a Lote"><i class="fas fa-plus-circle text-[10px]"></i></button>
                    <button onclick="abrirModalEditarModem(${m.id})" class="h-9 w-9 rounded-xl bg-blue-50 text-blue-400 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center" title="Editar Equipo"><i class="fas fa-pencil-alt text-[10px]"></i></button>
                    <button onclick="abrirModalHistorial(${m.id}, '${m.sn}')" class="h-9 w-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center" title="Ver Historial"><i class="fas fa-history text-[10px]"></i></button>
                    <button onclick="abrirModalEstadoInv(${m.id}, '${m.estado_inventario}')" class="h-9 w-9 rounded-xl bg-amber-50 text-amber-400 hover:bg-amber-600 hover:text-white transition-all flex items-center justify-center" title="Cambiar Estado"><i class="fas fa-exchange-alt text-[10px]"></i></button>
                </div>
            </td>
        </tr>
    `).join('');
    updateBulkBar();
}

function toggleSelectModem(id, checked) {
    if (checked) window.selectedModems.add(id);
    else window.selectedModems.delete(id);
    renderInventario();
}

function toggleSelectAllInv(checked) {
    if (checked) {
        window.inventario.forEach(m => {
            if (window.filtroEstadoInv === 'TODOS' || m.estado_inventario === window.filtroEstadoInv) {
                window.selectedModems.add(m.id);
            }
        });
    } else {
        window.selectedModems.clear();
    }
    renderInventario();
}

function deseleccionarTodo() {
    window.selectedModems.clear();
    document.getElementById('select-all-inv').checked = false;
    renderInventario();
}

function updateBulkBar() {
    const bar = document.getElementById('bulk-actions-bar');
    const count = document.getElementById('bulk-count');
    if (window.selectedModems.size > 0) {
        bar.classList.remove('hidden');
        count.textContent = window.selectedModems.size;
    } else {
        bar.classList.add('hidden');
    }
}

function abrirModalAsignarLoteMasivo() {
    const activeLotes = window.lotes.filter(l => l.estado === 'NUEVO' || l.estado === 'REVISION');
    if (activeLotes.length === 0) {
        showToast('No hay órdenes de producción activas', 'error');
        return;
    }
    document.getElementById('select-lote-rapido').innerHTML = activeLotes.map(l => `<option value="${l.id}">${l.nombre} (${l.estado})</option>`).join('');
    // Cambiamos el onclick del botón confirmar para que use la función masiva
    const btn = document.querySelector('#modal-asignar-lote button[onclick="confirmarAsignacionRapida()"]');
    btn.setAttribute('onclick', 'confirmarAsignacionMasiva()');
    document.getElementById('modal-asignar-lote').classList.remove('hidden');
}

async function confirmarAsignacionMasiva() {
    const lote_id = document.getElementById('select-lote-rapido').value;
    const modem_ids = Array.from(window.selectedModems);

    try {
        const res = await fetch('api.php?controller=LoteModems&action=asignarMasivo', {
            method: 'POST',
            body: JSON.stringify({ lote_id, modem_ids })
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast(data.message, 'success');
            cerrarModalAsignarLote();
            deseleccionarTodo();
            fetchInventario();
            // Restaurar el onclick original por si acaso
            document.querySelector('#modal-asignar-lote button[onclick="confirmarAsignacionMasiva()"]').setAttribute('onclick', 'confirmarAsignacionRapida()');
        } else { showToast(data.message, 'error'); }
    } catch (e) { showToast('Error en la petición', 'error'); }
}

function filtrarInventario(query) {
    const q = query.toLowerCase();
    const filtered = window.inventario.filter(m => m.sn.toLowerCase().includes(q));
    renderInventario(filtered);
}

function filtrarInventarioEstado(estado) {
    window.filtroEstadoInv = estado;
    document.querySelectorAll('.filter-inv-btn').forEach(btn => {
        if (btn.dataset.estado === estado) {
            btn.classList.replace('bg-white', 'bg-slate-900');
            btn.classList.replace('text-slate-400', 'text-white');
            btn.classList.add('shadow-lg');
        } else {
            btn.classList.replace('bg-slate-900', 'bg-white');
            btn.classList.replace('text-white', 'text-slate-400');
            btn.classList.remove('shadow-lg');
        }
    });
    renderInventario();
}

// --- MODALES INVENTARIO ---

function abrirModalEditarModem(id) {
    const m = window.inventario.find(mod => mod.id == id);
    if (!m) return;
    window.currentModemId = id;
    document.getElementById('edit-mod-sn').value = m.sn;
    document.getElementById('edit-mod-modelo').innerHTML = window.modelos.map(mod => `<option value="${mod.id}" ${mod.id == m.modelo_id ? 'selected' : ''}>${mod.nombre}</option>`).join('');
    document.getElementById('edit-mod-ssid').value = m.ssid;
    document.getElementById('edit-mod-pass').value = m.password;
    document.getElementById('modal-editar-modem').classList.remove('hidden');
}

function cerrarModalEditarModem() { document.getElementById('modal-editar-modem').classList.add('hidden'); }

async function guardarEdicionModemInv() {
    const payload = {
        id: window.currentModemId,
        sn: document.getElementById('edit-mod-sn').value,
        modelo_id: document.getElementById('edit-mod-modelo').value,
        ssid: document.getElementById('edit-mod-ssid').value,
        password: document.getElementById('edit-mod-pass').value
    };

    try {
        const res = await fetch('api.php?controller=Modems&action=guardar', {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast('Equipo actualizado', 'success');
            cerrarModalEditarModem();
            fetchInventario();
        } else { showToast(data.message, 'error'); }
    } catch (e) { showToast('Error al guardar', 'error'); }
}

function abrirModalAsignarLote(id) {
    window.currentModemId = id;
    const activeLotes = window.lotes.filter(l => l.estado === 'NUEVO' || l.estado === 'REVISION');
    if (activeLotes.length === 0) {
        showToast('No hay órdenes de producción activas', 'error');
        return;
    }
    document.getElementById('select-lote-rapido').innerHTML = activeLotes.map(l => `<option value="${l.id}">${l.nombre} (${l.estado})</option>`).join('');
    document.getElementById('modal-asignar-lote').classList.remove('hidden');
}

function cerrarModalAsignarLote() { document.getElementById('modal-asignar-lote').classList.add('hidden'); }

async function confirmarAsignacionRapida() {
    const lote_id = document.getElementById('select-lote-rapido').value;
    const modem_id = window.currentModemId;
    const modem = window.inventario.find(m => m.id == modem_id);

    try {
        const res = await fetch('api.php?controller=Modems&action=guardar', {
            method: 'POST',
            body: JSON.stringify({ 
                lote_id, 
                sn: modem.sn, 
                modelo_id: modem.modelo_id,
                ssid: modem.ssid,
                password: modem.password
            })
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast('Asignado correctamente', 'success');
            cerrarModalAsignarLote();
        } else { showToast(data.message, 'error'); }
    } catch (e) { showToast('Error en la petición', 'error'); }
}

async function abrirModalHistorial(id, sn) {
    document.getElementById('hist-modem-sn').textContent = `SN: ${sn}`;
    const container = document.getElementById('historial-container');
    container.innerHTML = '<div class="p-10 text-center text-slate-400"><i class="fas fa-circle-notch fa-spin mr-2"></i> Cargando...</div>';
    document.getElementById('modal-historial').classList.remove('hidden');

    try {
        const res = await fetch(`api.php?controller=Historial&action=listarPorModem&modem_id=${id}`);
        const data = await res.json();
        if (data.status === 'success') {
            if (data.data.length === 0) {
                container.innerHTML = '<div class="p-10 text-center text-slate-400 italic">No hay movimientos registrados.</div>';
                return;
            }
            container.innerHTML = data.data.map(h => `
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex gap-4">
                    <div class="h-10 w-10 bg-white rounded-full border border-slate-200 flex items-center justify-center text-slate-400 shrink-0">
                        <i class="fas ${
                            h.tipo_movimiento === 'REGISTRO' ? 'fa-plus' : 
                            h.tipo_movimiento === 'ASIGNACION_LOTE' ? 'fa-tag' : 
                            h.tipo_movimiento === 'IMPRESION' ? 'fa-print' : 'fa-info'
                        } text-[10px]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-black uppercase italic text-slate-900">${h.tipo_movimiento.replace('_', ' ')}</span>
                            <span class="text-[8px] font-bold text-slate-400">${formatDate(h.fecha)}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">${h.notas || 'Sin observaciones'}</p>
                        ${h.lote_nombre ? `<p class="text-[8px] font-black text-blue-500 mt-2 uppercase tracking-widest"><i class="fas fa-folder-open mr-1"></i> ${h.lote_nombre}</p>` : ''}
                    </div>
                </div>
            `).join('');
        }
    } catch (e) { container.innerHTML = '<div class="p-10 text-center text-red-500 italic">Error al cargar historial.</div>'; }
}

function cerrarModalHistorial() { document.getElementById('modal-historial').classList.add('hidden'); }

function abrirModalEstadoInv(id, currentStatus) {
    window.currentModemId = id;
    document.getElementById('new-inv-status').value = currentStatus;
    document.getElementById('inv-status-notes').value = '';
    document.getElementById('modal-estado-inv').classList.remove('hidden');
}

function cerrarModalEstadoInv() { document.getElementById('modal-estado-inv').classList.add('hidden'); }

// --- NUEVO EQUIPO DESDE INVENTARIO ---

function abrirModalNuevoModemInv() {
    document.getElementById('inv-new-sn').value = '';
    document.getElementById('inv-new-modelo').innerHTML = window.modelos.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
    document.getElementById('inv-new-ssid').value = '';
    document.getElementById('inv-new-pass').value = '';
    document.getElementById('modal-nuevo-modem-inv').classList.remove('hidden');
    setTimeout(() => document.getElementById('inv-new-sn').focus(), 100);
}

function cerrarModalNuevoModemInv() { document.getElementById('modal-nuevo-modem-inv').classList.add('hidden'); }

// Auto-generar SSID basado en SN para el inventario
document.addEventListener('input', (e) => {
    if (e.target.id === 'inv-new-sn') {
        const sn = e.target.value.toUpperCase();
        e.target.value = sn;
        if (sn.length >= 4) {
            document.getElementById('inv-new-ssid').value = `WITMAC_${sn.slice(-4)}`;
        }
    }
});

async function guardarNuevoModemInv() {
    const sn = document.getElementById('inv-new-sn').value.toUpperCase();
    const modelo_id = document.getElementById('inv-new-modelo').value;
    const ssid = document.getElementById('inv-new-ssid').value;
    const password = document.getElementById('inv-new-pass').value;

    if (!sn || !modelo_id) return showToast('SN y Modelo son obligatorios', 'error');

    try {
        const res = await fetch('api.php?controller=Modems&action=guardar', {
            method: 'POST',
            body: JSON.stringify({ sn, modelo_id, ssid, password })
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast('Equipo registrado en inventario', 'success');
            cerrarModalNuevoModemInv();
            fetchInventario();
        } else { showToast(data.message, 'error'); }
    } catch (e) { showToast('Error al registrar equipo', 'error'); }
}

async function confirmarCambioEstadoInv() {
    const id = window.currentModemId;
    const nuevo = document.getElementById('new-inv-status').value;
    const notas = document.getElementById('inv-status-notes').value;

    try {
        const res = await fetch('api.php?controller=Modems&action=actualizarEstado', {
            method: 'POST',
            body: JSON.stringify({ id, nuevo_estado: nuevo, notas })
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast('Estado actualizado', 'success');
            cerrarModalEstadoInv();
            fetchInventario();
        } else { showToast(data.message, 'error'); }
    } catch (e) { showToast('Error en la petición', 'error'); }
}

// --- PRODUCCIÓN (FLUJO HÍBRIDO) ---

async function fetchModemsLote() {
    const res = await fetch(`api.php?controller=LoteModems&action=listarPorLote&lote_id=${window.currentLoteId}`);
    const data = await res.json();
    if (data.status === 'success') {
        window.modemsLote = data.data;
        document.getElementById('modems-table-body').innerHTML = data.data.map(m => `
            <tr class="hover:bg-slate-50 transition-all group">
                <td class="px-8 py-6 font-mono font-bold text-slate-800 text-sm">${m.sn}</td>
                <td class="px-8 py-6 text-xs text-slate-500 font-black uppercase italic">${m.modelo_nombre || '---'}</td>
                <td class="px-8 py-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-tighter">${m.ssid}</span>
                        <span class="text-[10px] font-mono text-slate-400 mt-0.5">${m.password}</span>
                    </div>
                </td>
                <td class="px-8 py-6 text-center">
                    <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest ${
                        m.estado_impresion === 'IMPRESO' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600'
                    }">${m.estado_impresion}</span>
                </td>
                <td class="px-8 py-6 text-right">
                    <button onclick="desvincularModem(${m.modem_id})" class="h-9 w-9 rounded-xl bg-slate-50 text-slate-300 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center" title="Quitar del Lote"><i class="fas fa-unlink text-[10px]"></i></button>
                </td>
            </tr>
        `).join('');
    }
}

async function desvincularModem(modem_id) {
    try {
        const res = await fetch('api.php?controller=LoteModems&action=desvincular', {
            method: 'POST',
            body: JSON.stringify({ lote_id: window.currentLoteId, modem_id: modem_id })
        });
        if ((await res.json()).status === 'success') {
            showToast('Equipo quitado del lote', 'success');
            fetchModemsLote();
        }
    } catch (e) { showToast('Error al desvincular', 'error'); }
}

document.getElementById('sn').addEventListener('blur', async (e) => {
    const sn = e.target.value.toUpperCase();
    if (sn.length < 4) return;

    try {
        const res = await fetch(`api.php?controller=Modems&action=buscarPorSN&sn=${sn}`);
        const data = await res.json();
        if (data.status === 'success') {
            const m = data.data;
            document.getElementById('modelo_id').value = m.modelo_id;
            document.getElementById('ssid').value = m.ssid;
            document.getElementById('password').value = m.password;
            document.getElementById('btn-submit-modem').textContent = 'Añadir a Lote';
            document.getElementById('btn-submit-modem').classList.replace('bg-slate-900', 'bg-blue-600');
            showToast('Equipo encontrado en inventario', 'success');
        } else {
            document.getElementById('btn-submit-modem').textContent = 'Registrar y Añadir';
            document.getElementById('btn-submit-modem').classList.replace('bg-blue-600', 'bg-slate-900');
        }
    } catch (e) {}
});

document.getElementById('modem-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const payload = { 
        lote_id: window.currentLoteId, 
        modelo_id: document.getElementById('modelo_id').value, 
        sn: document.getElementById('sn').value.toUpperCase(), 
        ssid: document.getElementById('ssid').value,
        password: document.getElementById('password').value 
    };

    try {
        const res = await fetch('api.php?controller=Modems&action=guardar', { 
            method: 'POST', 
            body: JSON.stringify(payload) 
        });
        const data = await res.json();
        if (data.status === 'success') { 
            showToast(data.message, 'success'); 
            cancelarEdicionModem();
            fetchModemsLote(); 
            fetchInventario();
        } else { showToast(data.message, 'error'); }
    } catch (e) { showToast('Error al guardar', 'error'); }
});

function cancelarEdicionModem() {
    window.currentModemId = null;
    const lastModeloId = document.getElementById('modelo_id').value;
    document.getElementById('modem-form').reset();
    document.getElementById('modelo_id').value = lastModeloId;
    document.getElementById('btn-submit-modem').textContent = 'Guardar Equipo';
    document.getElementById('btn-submit-modem').classList.replace('bg-blue-600', 'bg-slate-900');
    document.getElementById('sn').focus();
}

// --- IMPRESIÓN ---

async function renderImpresion(lote) {
    document.getElementById('imp-lote-name').textContent = lote.nombre;
    document.getElementById('imp-lote-status').textContent = lote.estado;
    
    const res = await fetch(`api.php?controller=LoteModems&action=listarPorLote&lote_id=${lote.id}`);
    const data = await res.json();
    
    if (data.status === 'success') {
        const modems = data.data;
        document.getElementById('imp-lote-count').textContent = `${modems.length} EQUIPOS`;
        
        document.getElementById('imp-modems-table-body').innerHTML = modems.map(m => `
            <tr class="hover:bg-slate-50 transition-all group">
                <td class="px-8 py-6 font-mono font-bold text-slate-800 text-sm">${m.sn}</td>
                <td class="px-8 py-6 text-xs text-slate-500 font-black uppercase italic">${m.modelo_nombre || '---'}</td>
                <td class="px-8 py-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-tighter">${m.ssid}</span>
                        <span class="text-[10px] font-mono text-slate-400 mt-0.5">${m.password}</span>
                    </div>
                </td>
                <td class="px-8 py-6 text-right">
                    <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest ${
                        m.estado_impresion === 'IMPRESO' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600'
                    }">${m.estado_impresion}</span>
                </td>
            </tr>
        `).join('');

        const resumen = {};
        modems.forEach(m => { resumen[m.modelo_nombre] = (resumen[m.modelo_nombre] || 0) + 1; });
        document.getElementById('imp-resumen-modelos').innerHTML = Object.entries(resumen).map(([nombre, cant]) => `
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-black uppercase text-slate-600 italic">${nombre}</span>
                <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-[10px] font-black">${cant}</span>
            </div>
        `).join('');
    }
}

// --- COMUNES / AUXILIARES ---

function switchView(view) {
    document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
    const navBtn = document.getElementById('nav-' + (view === 'produccion' ? 'dashboard' : view));
    if (navBtn) navBtn.classList.add('active');
    ['dashboard', 'inventario', 'produccion', 'impresion', 'modelos', 'templates', 'designer'].forEach(v => {
        const el = document.getElementById('view-' + v);
        if (el) el.classList.add('hidden');
    });
    const target = document.getElementById('view-' + view);
    if (target) target.classList.remove('hidden');
}

function showToast(msg, type) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `px-10 py-6 rounded-[2rem] shadow-2xl text-white font-black italic uppercase transition-all animate-bounce flex items-center gap-4 ${type === 'success' ? 'bg-blue-600' : 'bg-red-600'}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-double' : 'fa-exclamation-triangle'} text-xl"></i> ${msg}`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

function abrirConfirm(titulo, msg, callback) {
    document.getElementById('confirm-title').textContent = titulo;
    document.getElementById('confirm-msg').textContent = msg;
    const btn = document.getElementById('confirm-btn-exec');
    btn.onclick = callback;
    document.getElementById('modal-confirm').classList.remove('hidden');
}

function cerrarConfirm() { document.getElementById('modal-confirm').classList.add('hidden'); }

async function fetchModelos() {
    const res = await fetch('api.php?controller=ModeloModems&action=listar');
    const data = await res.json();
    if (data.status === 'success') {
        window.modelos = data.data;
        document.getElementById('modelo_id').innerHTML = window.modelos.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
        document.getElementById('modelos-table-body').innerHTML = window.modelos.map(m => `
            <tr class="hover:bg-slate-50 transition-all">
                <td class="px-8 py-7"><span class="text-slate-900 text-lg tracking-tighter">${m.nombre}</span></td>
                <td class="px-8 py-7">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2"><i class="fas fa-tag text-[8px] text-blue-500"></i><span class="text-[10px] text-slate-600">${m.template_primario || 'Sin asignar'}</span></div>
                        ${m.template_secundario ? `<div class="flex items-center gap-2"><i class="fas fa-tag text-[8px] text-indigo-400"></i><span class="text-[10px] text-slate-400">${m.template_secundario} (Trasera)</span></div>` : ''}
                    </div>
                </td>
                <td class="px-8 py-7 text-center"><span class="bg-slate-100 text-slate-900 px-4 py-2 rounded-xl font-black text-xs">${m.cant_etiquetas}</span></td>
                <td class="px-8 py-7 text-right">
                    <button onclick="editarModelo(${m.id})" class="h-10 w-10 bg-slate-50 text-slate-300 rounded-xl hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-cog text-xs"></i></button>
                </td>
            </tr>
        `).join('');
    }
}

async function fetchTemplates() {
    const res = await fetch('api.php?controller=EtiquetaTemplates&action=listar');
    const data = await res.json();
    if (data.status === 'success') {
        window.templates = data.data;
        document.getElementById('stat-templates').textContent = window.templates.length;
        document.getElementById('templates-grid').innerHTML = window.templates.map(t => `
            <div class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm flex flex-col group">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6"><i class="fas fa-vector-square"></i></div>
                <h4 class="font-black text-slate-900 text-xl uppercase mb-2">${t.nombre}</h4>
                <p class="text-[10px] text-slate-400 font-black mb-10">${t.ancho}x${t.alto} mm</p>
                <button onclick="abrirDesigner(${t.id})" class="w-full bg-slate-50 group-hover:bg-blue-600 group-hover:text-white py-4 rounded-2xl font-black text-[9px] transition-all">EDITAR DISEÑO</button>
            </div>
        `).join('');
    }
}

async function guardarLote() {
    const nombre = document.getElementById('lote-nombre').value;
    const desc = document.getElementById('lote-desc').value;
    const res = await fetch('api.php?controller=Lotes&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, descripcion: desc }) });
    if ((await res.json()).status === 'success') { cerrarModalLote(); fetchLotes(); showToast('Orden Iniciada', 'success'); }
}

async function guardarModelo() {
    const res = await fetch('api.php?controller=ModeloModems&action=guardar', { 
        method: 'POST', 
        body: JSON.stringify({ 
            id: window.currentModeloId,
            nombre: document.getElementById('mod-nombre').value, 
            cant_etiquetas: document.getElementById('mod-cant').value, 
            etiqueta_primaria_id: document.getElementById('mod-template-p').value, 
            etiqueta_secundaria_id: document.getElementById('mod-template-s').value || null 
        }) 
    });
    const data = await res.json();
    if (data.status === 'success') { cerrarModalModelo(); fetchModelos(); showToast('Modelo Guardado', 'success'); }
    else { showToast(data.message, 'error'); }
}

function editarModelo(id) {
    const m = window.modelos.find(mod => mod.id == id);
    if (!m) return;
    window.currentModeloId = id;
    abrirModalModelo();
    document.getElementById('mod-nombre').value = m.nombre;
    document.getElementById('mod-cant').value = m.cant_etiquetas;
    document.getElementById('mod-template-p').value = m.etiqueta_primaria_id;
    document.getElementById('mod-template-s').value = m.etiqueta_secundaria_id || '';
}

async function guardarTemplate() {
    const nombre = document.getElementById('tpl-nombre').value;
    const ancho = document.getElementById('tpl-ancho').value;
    const alto = document.getElementById('tpl-alto').value;
    if (!nombre || !ancho || !alto) return showToast('Campos obligatorios', 'error');
    const res = await fetch('api.php?controller=EtiquetaTemplates&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, ancho, alto, config_json: '[]' }) });
    const data = await res.json();
    if (data.status === 'success') { cerrarModalTemplate(); fetchTemplates(); showToast('Formato Creado', 'success'); }
    else { showToast(data.message, 'error'); }
}

function abrirModalLote() { document.getElementById('modal-lote').classList.remove('hidden'); }
function cerrarModalLote() { document.getElementById('modal-lote').classList.add('hidden'); }
function abrirModalModelo() { 
    if (!window.currentModeloId) {
        document.getElementById('mod-nombre').value = '';
        document.getElementById('mod-cant').value = '1';
    }
    const options = window.templates.map(t => `<option value="${t.id}">${t.nombre} (${t.ancho}x${t.alto}mm)</option>`).join('');
    document.getElementById('mod-template-p').innerHTML = options;
    document.getElementById('mod-template-s').innerHTML = '<option value="">Ninguna</option>' + options;
    document.getElementById('modal-modelo').classList.remove('hidden'); 
}
function cerrarModalModelo() { window.currentModeloId = null; document.getElementById('modal-modelo').classList.add('hidden'); }
function abrirModalTemplate() { document.getElementById('modal-template').classList.remove('hidden'); }
function cerrarModalTemplate() { document.getElementById('modal-template').classList.add('hidden'); }

function imprimirLote() { window.open(`print_lote.php?lote_id=${window.currentLoteId}`, '_blank'); }
