// Variables Globales (Disponibles para ambos archivos)
window.currentLoteId = null;
window.lotes = [];
window.modelos = [];
window.templates = [];

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    fetchLotes();
    fetchModelos();
    fetchTemplates();
    switchView('dashboard'); // Forzar vista inicial
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

function renderLotesDashboard() {
    const container = document.getElementById('view-dashboard');
    if (!container) return;
    
    container.innerHTML = window.lotes.map(l => `
        <div onclick="seleccionarLote(${l.id})" class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm hover:shadow-2xl transition-all cursor-pointer group">
            <div class="h-16 w-16 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all mb-8 shadow-inner">
                <i class="fas fa-barcode text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 italic tracking-tighter uppercase mb-2">${l.nombre}</h3>
            <span class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest ${
                l.estado === 'NUEVO' ? 'bg-blue-50 text-blue-600' : 
                l.estado === 'REVISION' ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600'
            }">${l.estado}</span>
        </div>
    `).join('');
}

async function seleccionarLote(id) {
    window.currentLoteId = id;
    const lote = window.lotes.find(l => l.id == id);
    switchView('produccion');
    document.getElementById('nav-produccion').classList.remove('hidden');
    document.getElementById('prod-lote-name').textContent = lote.nombre;
    document.getElementById('prod-lote-desc').textContent = lote.descripcion || 'Sin descripción';
    
    const badge = document.getElementById('lote-status-badge');
    badge.className = `px-6 py-2 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600`;
    badge.textContent = lote.estado;
    badge.classList.remove('hidden');

    renderBotonesAccion(lote.estado);
    document.getElementById('prod-form-card').style.display = (lote.estado === 'NUEVO') ? 'block' : 'none';
    fetchModems();
}

function renderBotonesAccion(estado) {
    const container = document.getElementById('lote-dynamic-actions');
    if (estado === 'NUEVO') container.innerHTML = `<button onclick="cambiarEstadoLote('REVISION')" class="bg-white/10 text-white px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest">Enviar a Revisión</button>`;
    else if (estado === 'REVISION') container.innerHTML = `<button onclick="cambiarEstadoLote('IMPRESO')" class="bg-white text-blue-600 px-8 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-xl">Marcar Impreso</button>`;
    else if (estado === 'IMPRESO') container.innerHTML = `<button onclick="cambiarEstadoLote('COMPLETADO')" class="bg-green-500 text-white px-8 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest">Finalizar</button>`;
    else container.innerHTML = '';
}

async function fetchModems() {
    const res = await fetch(`api.php?controller=Modems&action=listarPorLote&lote_id=${window.currentLoteId}`);
    const data = await res.json();
    if (data.status === 'success') {
        document.getElementById('modems-table-body').innerHTML = data.data.map(m => `
            <tr class="hover:bg-slate-50 transition-all">
                <td class="px-8 py-6 font-bold text-slate-800">${m.sn}<br><span class="text-[9px] text-blue-600 uppercase font-black tracking-widest">${m.ssid}</span></td>
                <td class="px-8 py-6 uppercase text-[10px] font-black text-slate-400">${m.estado}</td>
                <td class="px-8 py-6 text-right"><button onclick="cambiarEstadoModem(${m.id}, 'IMPRESO')" class="h-12 w-12 rounded-2xl bg-slate-50 text-slate-300 hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-print"></i></button></td>
            </tr>
        `).join('');
    }
}

async function fetchModelos() {
    const res = await fetch('api.php?controller=ModeloModems&action=listar');
    const data = await res.json();
    if (data.status === 'success') {
        window.modelos = data.data;
        document.getElementById('modelo_id').innerHTML = window.modelos.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
        document.getElementById('modelos-table-body').innerHTML = window.modelos.map(m => `
            <tr class="hover:bg-slate-50"><td class="px-8 py-7 uppercase">${m.nombre}</td><td class="px-8 py-7 text-center font-black">${m.cant_etiquetas}</td><td class="px-8 py-7 text-right"><i class="fas fa-cog text-slate-200"></i></td></tr>
        `).join('');
    }
}

async function fetchTemplates() {
    const res = await fetch('api.php?controller=EtiquetaTemplates&action=listar');
    const data = await res.json();
    if (data.status === 'success') {
        window.templates = data.data;
        document.getElementById('templates-grid').innerHTML = window.templates.map(t => `
            <div class="bg-white p-10 rounded-[3rem] border border-slate-200 shadow-sm flex flex-col group">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6"><i class="fas fa-vector-square"></i></div>
                <h4 class="font-black text-slate-900 text-xl uppercase mb-2">${t.nombre}</h4>
                <p class="text-[10px] text-slate-400 font-black mb-10">${t.ancho}x${t.alto} mm</p>
                <button onclick="abrirDiseñador(${t.id})" class="w-full bg-slate-50 group-hover:bg-blue-600 group-hover:text-white py-4 rounded-2xl font-black text-[9px] transition-all">EDITAR DISEÑO</button>
            </div>
        `).join('');
    }
}

// Helpers
function switchView(view) {
    document.querySelectorAll('.sidebar-item').forEach(item => item.classList.remove('active'));
    const navBtn = document.getElementById('nav-' + (view === 'produccion' ? 'dashboard' : view));
    if (navBtn) navBtn.classList.add('active');

    ['dashboard', 'produccion', 'modelos', 'templates', 'designer'].forEach(v => {
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

// Forms
async function guardarLote() {
    const nombre = document.getElementById('lote-nombre').value;
    const desc = document.getElementById('lote-desc').value;
    const res = await fetch('api.php?controller=Lotes&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, descripcion: desc }) });
    if ((await res.json()).status === 'success') { cerrarModalLote(); fetchLotes(); showToast('Orden Iniciada', 'success'); }
}

async function guardarModelo() {
    const nombre = document.getElementById('mod-nombre').value;
    const cant = document.getElementById('mod-cant').value;
    const res = await fetch('api.php?controller=ModeloModems&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, cant_etiquetas: cant }) });
    if ((await res.json()).status === 'success') { cerrarModalModelo(); fetchModelos(); showToast('Modelo Guardado', 'success'); }
}

async function guardarTemplate() {
    const nombre = document.getElementById('tpl-nombre').value;
    const ancho = document.getElementById('tpl-ancho').value;
    const alto = document.getElementById('tpl-alto').value;
    const res = await fetch('api.php?controller=EtiquetaTemplates&action=guardar', { method: 'POST', body: JSON.stringify({ nombre, ancho, alto, config_json: '[]' }) });
    if ((await res.json()).status === 'success') { cerrarModalTemplate(); fetchTemplates(); showToast('Formato Creado', 'success'); }
}

document.getElementById('modem-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('api.php?controller=Modems&action=guardar', { 
        method: 'POST', 
        body: JSON.stringify({ 
            lote_id: window.currentLoteId, 
            modelo_id: document.getElementById('modelo_id').value, 
            sn: document.getElementById('sn').value, 
            password: document.getElementById('password').value 
        }) 
    });
    if ((await res.json()).status === 'success') { showToast('Equipo Guardado', 'success'); document.getElementById('sn').value = ''; document.getElementById('sn').focus(); fetchModems(); }
});

// Modals
function abrirModalLote() { document.getElementById('modal-lote').classList.remove('hidden'); }
function cerrarModalLote() { document.getElementById('modal-lote').classList.add('hidden'); }
function abrirModalModelo() { document.getElementById('modal-modelo').classList.remove('hidden'); }
function cerrarModalModelo() { document.getElementById('modal-modelo').classList.add('hidden'); }
function abrirModalTemplate() { document.getElementById('modal-template').classList.remove('hidden'); }
function cerrarModalTemplate() { document.getElementById('modal-template').classList.add('hidden'); }
