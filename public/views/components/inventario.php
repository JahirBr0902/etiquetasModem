<div id="view-inventario" class="hidden space-y-8">
    <div class="flex justify-between items-center px-4 font-black italic uppercase">
        <div>
            <h3 class="text-xl">Inventario de Equipos</h3>
            <p class="text-[9px] text-slate-400 tracking-widest mt-1 not-italic">Control Global de Módems y Estados</p>
        </div>
        <div class="flex gap-3">
            <button onclick="abrirModalNuevoModemInv()" class="bg-blue-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-200 hover:scale-105 transition-all flex items-center gap-2"><i class="fas fa-plus"></i> Nuevo Equipo</button>
            <button onclick="fetchInventario()" class="h-12 w-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all shadow-sm"><i class="fas fa-sync-alt"></i></button>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <!-- Barra de Acciones Masivas (Oculta por defecto) -->
        <div id="bulk-actions-bar" class="hidden bg-indigo-600 p-4 flex justify-between items-center animate-pulse">
            <div class="flex items-center gap-4 ml-4">
                <span class="h-8 w-8 bg-white/20 rounded-lg flex items-center justify-center text-white text-[10px] font-black" id="bulk-count">0</span>
                <span class="text-white text-[10px] font-black uppercase tracking-widest italic">Equipos Seleccionados</span>
            </div>
            <div class="flex gap-2">
                <button onclick="abrirModalAsignarLoteMasivo()" class="bg-white text-indigo-600 px-6 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-xl">Asignar a Lote</button>
                <button onclick="deseleccionarTodo()" class="text-white/60 hover:text-white px-4 py-2 text-[9px] font-black uppercase">Cancelar</button>
            </div>
        </div>

        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-wrap gap-4 items-center justify-between">
            <div class="relative w-full md:w-72">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="text" id="inventario-search" oninput="filtrarInventario(this.value)" class="w-full bg-white border border-slate-200 rounded-xl py-3 pl-12 pr-4 font-bold outline-none focus:border-blue-600 transition-all text-xs" placeholder="Buscar por SN...">
            </div>
            <div class="flex gap-2">
                <button onclick="filtrarInventarioEstado('TODOS')" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-slate-900 text-white shadow-lg filter-inv-btn" data-estado="TODOS">Todos</button>
                <button onclick="filtrarInventarioEstado('BODEGA')" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-slate-400 border border-slate-100 filter-inv-btn" data-estado="BODEGA">Bodega</button>
                <button onclick="filtrarInventarioEstado('PRESTADO')" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-slate-400 border border-slate-100 filter-inv-btn" data-estado="PRESTADO">Prestado</button>
                <button onclick="filtrarInventarioEstado('DAÑADO')" class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest bg-white text-slate-400 border border-slate-100 filter-inv-btn" data-estado="DAÑADO">Dañado</button>
            </div>
        </div>
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                <tr>
                    <th class="px-8 py-6 w-10">
                        <input type="checkbox" id="select-all-inv" onclick="toggleSelectAllInv(this.checked)" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    </th>
                    <th class="px-4 py-6">Equipo / Serie</th>
                    <th class="px-8 py-6">Modelo</th>
                    <th class="px-8 py-6">Acceso WiFi</th>
                    <th class="px-8 py-6">Estado Inventario</th>
                    <th class="px-8 py-6 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="inventario-table-body" class="divide-y divide-slate-100 font-bold">
                <!-- Se llena por JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL HISTORIAL -->
<div id="modal-historial" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[300] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-2xl p-10 flex flex-col max-h-[80vh]">
        <div class="flex justify-between items-start mb-8">
            <div class="italic font-black uppercase">
                <h3 class="text-2xl tracking-tighter">Historial del Equipo</h3>
                <p id="hist-modem-sn" class="text-blue-600 text-sm mt-1">SN: ---</p>
            </div>
            <button onclick="cerrarModalHistorial()" class="h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
        </div>
        
        <div id="historial-container" class="flex-1 overflow-y-auto pr-4 space-y-4 custom-scrollbar">
            <!-- Items de historial -->
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex gap-4">
            <button onclick="cerrarModalHistorial()" class="w-full bg-slate-900 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl">Entendido</button>
        </div>
    </div>
</div>

<!-- MODAL CAMBIO ESTADO -->
<div id="modal-estado-inv" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[300] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3 class="text-2xl tracking-tighter">Cambiar Estado</h3>
        <div class="space-y-4 not-italic">
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Nuevo Estado</label>
                <select id="new-inv-status" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none">
                    <option value="BODEGA">En Bodega</option>
                    <option value="PRESTADO">Prestado / Servicio</option>
                    <option value="DAÑADO">Dañado / Defectuoso</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Notas / Observaciones</label>
                <textarea id="inv-status-notes" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium h-24 outline-none font-sans" placeholder="¿Por qué se cambia el estado?"></textarea>
            </div>
        </div>
        <div class="flex gap-4">
            <button onclick="cerrarModalEstadoInv()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest text-slate-400">Cancelar</button>
            <button onclick="confirmarCambioEstadoInv()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] shadow-xl tracking-widest">Actualizar</button>
        </div>
    </div>
</div>

<!-- MODAL EDITAR MÓDEM -->
<div id="modal-editar-modem" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[300] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3 class="text-2xl tracking-tighter">Editar Equipo</h3>
        <div class="space-y-4 not-italic">
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Número de Serie (SN)</label>
                <input type="text" id="edit-mod-sn" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none uppercase" readonly title="El SN no se puede editar para mantener historial">
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Modelo</label>
                <select id="edit-mod-modelo" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none"></select>
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">SSID / Red</label>
                <input type="text" id="edit-mod-ssid" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none">
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Password</label>
                <input type="text" id="edit-mod-pass" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none">
            </div>
        </div>
        <div class="flex gap-4">
            <button onclick="cerrarModalEditarModem()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest text-slate-400">Cancelar</button>
            <button onclick="guardarEdicionModemInv()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] shadow-xl tracking-widest">Guardar Cambios</button>
        </div>
    </div>
</div>

<!-- MODAL NUEVO EQUIPO -->
<div id="modal-nuevo-modem-inv" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[300] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3 class="text-2xl tracking-tighter">Nuevo Equipo</h3>
        <div class="space-y-4 not-italic">
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Número de Serie (SN)</label>
                <input type="text" id="inv-new-sn" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none uppercase" placeholder="Escanear o Escribir SN">
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Modelo</label>
                <select id="inv-new-modelo" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none"></select>
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">SSID / Red</label>
                <input type="text" id="inv-new-ssid" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none" placeholder="WITMAC_XXXX">
            </div>
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Password</label>
                <input type="text" id="inv-new-pass" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none" placeholder="Password">
            </div>
        </div>
        <div class="flex gap-4">
            <button onclick="cerrarModalNuevoModemInv()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest text-slate-400">Cancelar</button>
            <button onclick="guardarNuevoModemInv()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] shadow-xl tracking-widest">Registrar Equipo</button>
        </div>
    </div>
</div>

<!-- MODAL ASIGNAR A LOTE RÁPIDO -->
<div id="modal-asignar-lote" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[300] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3 class="text-2xl tracking-tighter">Asignar a Lote</h3>
        <p class="text-[10px] text-slate-400 not-italic normal-case">Selecciona una orden de producción activa para añadir este equipo.</p>
        <div class="space-y-4 not-italic">
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Seleccionar Orden</label>
                <select id="select-lote-rapido" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none"></select>
            </div>
        </div>
        <div class="flex gap-4">
            <button onclick="cerrarModalAsignarLote()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest text-slate-400">Cancelar</button>
            <button onclick="confirmarAsignacionRapida()" class="flex-1 bg-indigo-600 text-white py-5 rounded-2xl text-[10px] shadow-xl tracking-widest">Añadir ahora</button>
        </div>
    </div>
</div>
