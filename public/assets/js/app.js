// Variables Globales
window.currentLoteId = null;
window.currentModeloId = null;
window.currentModemId = null; 
window.lotes = [];
window.modelos = [];
window.templates = [];

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    fetchLotes();
    fetchModelos();
    fetchTemplates();
    switchView('dashboard');
});

async function fetchLotes() {
    try {
        const res = await fetch('api.php?controller=Lotes&action=listar');
        const data = await res.json();
        if (data.status === 'success') {
            window.lotes = data.data;
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
    if (!filtered) {
        document.getElementById('stat-lotes').textContent = window.lotes.length;
        document.getElementById('stat-templates').textContent = window.templates.length;
    }
    container.innerHTML = items.map(l => {
        let dateInfo = `<div class="flex flex-col gap-1">
            <span class="text-[9px] text-slate-400 font-bold uppercase">Creado: ${formatDate(l.fecha_creacion)}</span>`;
        
        if (l.estado === 'COMPLETADO' || l.estado === 'IMPRESO') {
            dateInfo += `<span class="text-[9px] text-green-500 font-black uppercase italic">Finalizado: ${formatDate(l.fecha_finalizacion)}</span>`;
        } else if (l.fecha_actualizacion) {
            dateInfo += `<span class="text-[9px] text-blue-400 font-bold uppercase">Act: ${formatDate(l.fecha_actualizacion)}</span>`;
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
    
    // Si el lote está en REVISION o IMPRESO, vamos a la vista de impresión
    if (lote.estado === 'REVISION' || lote.estado === 'IMPRESO') {
        switchView('impresion');
        renderImpresion(lote);
    } else {
        switchView('produccion');
        document.getElementById('nav-produccion').classList.remove('hidden');
        document.getElementById('prod-lote-name').textContent = lote.nombre;
        document.getElementById('prod-lote-desc').textContent = lote.descripcion || 'Sin descripción';
        const badge = document.getElementById('lote-status-badge');
        if (badge) {
            badge.className = `px-6 py-2 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600`;
            badge.textContent = lote.estado;
            badge.classList.remove('hidden');
        }
        renderBotonesAccion(lote.estado);
        document.getElementById('prod-form-card').style.display = (lote.estado === 'NUEVO') ? 'block' : 'none';
        fetchModems();
    }
}

async function renderImpresion(lote) {
    document.getElementById('imp-lote-name').textContent = lote.nombre;
    document.getElementById('imp-lote-status').textContent = lote.estado;
    
    const res = await fetch(`api.php?controller=Modems&action=listarPorLote&lote_id=${lote.id}`);
    const data = await res.json();
    
    if (data.status === 'success') {
        const modems = data.data;
        window.modemsLote = modems;
        document.getElementById('imp-lote-count').textContent = `${modems.length} EQUIPOS`;
        
        // Render Tabla
        document.getElementById('imp-modems-table-body').innerHTML = modems.map(m => `
            <tr class="hover:bg-slate-50 transition-all group">
                <td class="px-8 py-6 font-mono font-bold text-slate-800 text-sm">${m.sn}</td>
                <td class="px-8 py-6 text-xs text-slate-500 font-black uppercase italic">${m.modelo_nombre || 'Desconocido'}</td>
                <td class="px-8 py-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-tighter">${m.ssid}</span>
                        <span class="text-[10px] font-mono text-slate-400 mt-0.5">${m.password}</span>
                    </div>
                </td>
                <td class="px-8 py-6 text-right">
                    <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest ${
                        m.estado === 'IMPRESO' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600'
                    }">${m.estado}</span>
                </td>
            </tr>
        `).join('');

        // Render Resumen de Modelos
        const resumen = {};
        modems.forEach(m => {
            resumen[m.modelo_nombre] = (resumen[m.modelo_nombre] || 0) + 1;
        });

        document.getElementById('imp-resumen-modelos').innerHTML = Object.entries(resumen).map(([nombre, cant]) => `
            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <span class="text-[10px] font-black uppercase text-slate-600 italic">${nombre}</span>
                <span class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-[10px] font-black">${cant}</span>
            </div>
        `).join('');
    }
}

function imprimirLote() {
    if (!window.currentLoteId) return;
    window.open(`print_lote.php?lote_id=${window.currentLoteId}`, '_blank');
}

async function cambiarEstadoLote(nuevoEstado) {
    if (!window.currentLoteId) return;
    try {
        const res = await fetch('api.php?controller=Lotes&action=cambiarEstado', {
            method: 'POST',
            body: JSON.stringify({ lote_id: window.currentLoteId, nuevo_estado: nuevoEstado })
        });
        const data = await res.json();
        if (data.status === 'success') {
            showToast(`Lote actualizado a ${nuevoEstado}`, 'success');
            await fetchLotes();
            const lote = window.lotes.find(l => l.id == window.currentLoteId);
            seleccionarLote(lote.id);
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
    if (estado === 'NUEVO') container.innerHTML = `<button onclick="cambiarEstadoLote('REVISION')" class="bg-white/10 text-white px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-white/20 transition-all">Enviar a Revisión</button>`;
    else if (estado === 'REVISION') container.innerHTML = `<button onclick="cambiarEstadoLote('IMPRESO')" class="bg-white text-blue-600 px-8 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-xl">Marcar Impreso</button>`;
    else if (estado === 'IMPRESO') container.innerHTML = `<button onclick="cambiarEstadoLote('COMPLETADO')" class="bg-green-500 text-white px-8 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest">Finalizar</button>`;
    else container.innerHTML = '';
}

async function fetchModems() {
    const res = await fetch(`api.php?controller=Modems&action=listarPorLote&lote_id=${window.currentLoteId}`);
    const data = await res.json();
    if (data.status === 'success') {
        window.modemsLote = data.data;
        document.getElementById('modems-table-body').innerHTML = data.data.map(m => `
            <tr class="hover:bg-slate-50 transition-all group">
                <td class="px-8 py-6 font-mono font-bold text-slate-800 text-sm">${m.sn}</td>
                <td class="px-8 py-6 text-xs text-slate-500 font-black uppercase italic">${m.modelo_nombre || 'Desconocido'}</td>
                <td class="px-8 py-6">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-blue-600 uppercase tracking-tighter">${m.ssid}</span>
                        <span class="text-[10px] font-mono text-slate-400 mt-0.5">${m.password}</span>
                    </div>
                </td>
                <td class="px-8 py-6 uppercase text-[9px] font-black tracking-widest ${m.estado === 'IMPRESO' ? 'text-green-500' : 'text-amber-500'}">
                    ${m.estado}
                </td>
                <td class="px-8 py-6 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="editarModem(${m.id})" class="h-10 w-10 rounded-xl bg-slate-50 text-slate-300 hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-inner" title="Editar"><i class="fas fa-pencil-alt text-[10px]"></i></button>
                        <button onclick="confirmarEliminarModem(${m.id})" class="h-10 w-10 rounded-xl bg-slate-50 text-slate-300 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center shadow-inner" title="Eliminar"><i class="fas fa-trash-alt text-[10px]"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

function editarModem(id) {
    const m = window.modemsLote.find(mod => mod.id == id);
    if (!m) return;
    
    window.currentModemId = id;
    document.getElementById('modelo_id').value = m.modelo_id;
    document.getElementById('sn').value = m.sn;
    document.getElementById('ssid').value = m.ssid;
    document.getElementById('password').value = m.password;
    
    document.querySelector('#prod-form-card h3').textContent = 'Editar Equipo';
    document.getElementById('btn-submit-modem').textContent = 'Actualizar';
    document.getElementById('btn-submit-modem').classList.replace('bg-slate-900', 'bg-blue-600');
    document.getElementById('btn-cancel-edit').classList.remove('hidden');
    
    document.getElementById('sn').focus();
}

function cancelarEdicionModem() {
    window.currentModemId = null;
    const lastModeloId = document.getElementById('modelo_id').value; // Guardar antes de reset
    document.getElementById('modem-form').reset();
    document.getElementById('modelo_id').value = lastModeloId; // Restaurar
    
    document.querySelector('#prod-form-card h3').textContent = 'Agregar Equipo';
    document.getElementById('btn-submit-modem').textContent = 'Guardar Equipo';
    document.getElementById('btn-submit-modem').classList.replace('bg-blue-600', 'bg-slate-900');
    document.getElementById('btn-cancel-edit').classList.add('hidden');
}

function confirmarEliminarModem(id) {
    abrirConfirm('Eliminar Equipo', '¿Estás seguro de que deseas quitar este módem del lote? No podrás recuperarlo.', () => ejecutarEliminarModem(id));
}

async function ejecutarEliminarModem(id) {
    try {
        const res = await fetch(`api.php?controller=Modems&action=eliminar&id=${id}`);
        if ((await res.json()).status === 'success') {
            showToast('Equipo Eliminado', 'success');
            fetchModems();
            cerrarConfirm();
        }
    } catch (e) { showToast('Error al eliminar', 'error'); }
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
        const modeloSelect = document.getElementById('modelo_id');
        modeloSelect.innerHTML = window.modelos.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
        
        // Restaurar último modelo seleccionado
        const lastModeloId = localStorage.getItem('last_modelo_id');
        if (lastModeloId) {
            modeloSelect.value = lastModeloId;
        }

        document.getElementById('modelos-table-body').innerHTML = window.modelos.map(m => `
            <tr class="hover:bg-slate-50 transition-all">
                <td class="px-8 py-7">
                    <div class="flex flex-col"><span class="text-slate-900 text-lg tracking-tighter">${m.nombre}</span><span class="text-[8px] text-slate-400 font-black tracking-widest mt-1">Registrado en sistema</span></div>
                </td>
                <td class="px-8 py-7">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2"><i class="fas fa-tag text-[8px] text-blue-500"></i><span class="text-[10px] text-slate-600">${m.template_primario || 'Sin asignar'}</span></div>
                        ${m.template_secundario ? `<div class="flex items-center gap-2"><i class="fas fa-tag text-[8px] text-indigo-400"></i><span class="text-[10px] text-slate-400">${m.template_secundario} (Trasera)</span></div>` : ''}
                    </div>
                </td>
                <td class="px-8 py-7 text-center"><span class="bg-slate-100 text-slate-900 px-4 py-2 rounded-xl font-black text-xs">${m.cant_etiquetas}</span></td>
                <td class="px-8 py-7 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="verPruebaPDF(${m.id})" class="h-10 px-4 bg-slate-50 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all flex items-center gap-2 text-[9px] font-black uppercase italic"><i class="fas fa-file-pdf"></i> Prueba</button>
                        <button onclick="editarModelo(${m.id})" class="h-10 w-10 bg-slate-50 text-slate-300 rounded-xl hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-cog text-xs"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }
}

function verPruebaPDF(id) { window.open(`print_test.php?id=${id}`, '_blank'); }

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

function switchView(view) {
    document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
    const navBtn = document.getElementById('nav-' + (view === 'produccion' ? 'dashboard' : view));
    if (navBtn) navBtn.classList.add('active');
    ['dashboard', 'produccion', 'impresion', 'modelos', 'templates', 'designer'].forEach(v => {
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

async function guardarLote() {
    const nombre = document.getElementById('lote-nombre').value;
    const desc = document.getElementById('lote-desc').value;
    const res = await fetch('api.php?controller=Lotes&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, descripcion: desc }) });
    if ((await res.json()).status === 'success') { cerrarModalLote(); fetchLotes(); showToast('Orden Iniciada', 'success'); }
}

async function guardarModelo() {
    const nombre = document.getElementById('mod-nombre').value;
    const cant = document.getElementById('mod-cant').value;
    const p_id = document.getElementById('mod-template-p').value;
    const s_id = document.getElementById('mod-template-s').value;
    const payload = { nombre, cant_etiquetas: cant, etiqueta_primaria_id: p_id, etiqueta_secundaria_id: s_id || null };
    if (window.currentModeloId) payload.id = window.currentModeloId;
    const res = await fetch('api.php?controller=ModeloModems&action=guardar', { method: 'POST', body: JSON.stringify(payload) });
    if ((await res.json()).status === 'success') { cerrarModalModelo(); fetchModelos(); showToast(window.currentModeloId ? 'Modelo Actualizado' : 'Modelo Guardado', 'success'); }
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
    document.querySelector('#modal-modelo h3').textContent = 'Editar Modelo';
}

async function guardarTemplate() {
    const nombre = document.getElementById('tpl-nombre').value;
    const ancho = document.getElementById('tpl-ancho').value;
    const alto = document.getElementById('tpl-alto').value;
    const res = await fetch('api.php?controller=EtiquetaTemplates&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, ancho, alto, config_json: '[]' }) });
    if ((await res.json()).status === 'success') { cerrarModalTemplate(); fetchTemplates(); showToast('Formato Creado', 'success'); }
}

document.getElementById('sn').addEventListener('input', (e) => {
    const sn = e.target.value.toUpperCase();
    e.target.value = sn; // Forzar mayúsculas
    if (sn.length >= 4) {
        const last4 = sn.slice(-4);
        document.getElementById('ssid').value = `WITMAC_${last4}`;
    }
});

document.getElementById('modem-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const modelo_id = document.getElementById('modelo_id').value;
    localStorage.setItem('last_modelo_id', modelo_id); // Guardar en cache

    const payload = { 
        lote_id: window.currentLoteId, 
        modelo_id: modelo_id, 
        sn: document.getElementById('sn').value, 
        ssid: document.getElementById('ssid').value,
        password: document.getElementById('password').value 
    };
    
    if (window.currentModemId) payload.id = window.currentModemId;

    const res = await fetch('api.php?controller=Modems&action=guardar', { 
        method: 'POST', 
        body: JSON.stringify(payload) 
    });
    
    if ((await res.json()).status === 'success') { 
        showToast(window.currentModemId ? 'Equipo Actualizado' : 'Equipo Guardado', 'success'); 
        cancelarEdicionModem(); // Limpia todo
        fetchModems(); 
    }
});

function abrirModalLote() { document.getElementById('modal-lote').classList.remove('hidden'); }
function cerrarModalLote() { document.getElementById('modal-lote').classList.add('hidden'); }
function abrirModalModelo() { 
    if (!window.currentModeloId) {
        document.getElementById('mod-nombre').value = '';
        document.getElementById('mod-cant').value = '1';
        document.querySelector('#modal-modelo h3').textContent = 'Nuevo Modelo';
    }
    const p_select = document.getElementById('mod-template-p');
    const s_select = document.getElementById('mod-template-s');
    const options = window.templates.map(t => `<option value="${t.id}">${t.nombre} (${t.ancho}x${t.alto}mm)</option>`).join('');
    p_select.innerHTML = options;
    s_select.innerHTML = '<option value="">Ninguna</option>' + options;
    document.getElementById('modal-modelo').classList.remove('hidden'); 
}
function cerrarModalModelo() { window.currentModeloId = null; document.getElementById('modal-modelo').classList.add('hidden'); }
function abrirModalTemplate() { document.getElementById('modal-template').classList.remove('hidden'); }
function cerrarModalTemplate() { document.getElementById('modal-template').classList.add('hidden'); }
