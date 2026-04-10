<div id="view-dashboard" class="space-y-10">
    
    <!-- HEADER ESTADÍSTICAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-black italic uppercase">
        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-200">
            <div class="flex justify-between items-start mb-4">
                <i class="fas fa-boxes text-2xl opacity-50"></i>
                <span class="text-[8px] tracking-[0.3em]">Total Lotes</span>
            </div>
            <div id="stat-lotes" class="text-4xl tracking-tighter">0</div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <i class="fas fa-print text-2xl text-blue-600 opacity-20"></i>
                <span class="text-[8px] text-slate-400 tracking-[0.3em]">Equipos Registrados</span>
            </div>
            <div id="stat-modems" class="text-4xl tracking-tighter text-slate-900">0</div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <i class="fas fa-vector-square text-2xl text-blue-600 opacity-20"></i>
                <span class="text-[8px] text-slate-400 tracking-[0.3em]">Formatos de Etiqueta</span>
            </div>
            <div id="stat-templates" class="text-4xl tracking-tighter text-slate-900">0</div>
        </div>
    </div>

    <!-- BUSCADOR Y ACCIÓN -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white p-6 rounded-[2.5rem] border border-slate-200 shadow-sm">
        <div class="relative w-full md:w-96">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
            <input type="text" id="dashboard-search" oninput="filtrarLotes(this.value)" class="w-full bg-slate-50 border border-slate-100 rounded-2xl py-4 pl-14 pr-6 font-bold outline-none focus:border-blue-600 transition-all text-xs" placeholder="Buscar orden por nombre...">
        </div>
        <button onclick="abrirModalLote()" class="w-full md:w-auto bg-slate-900 text-white px-10 py-4 rounded-2xl font-black italic uppercase text-[10px] tracking-widest shadow-xl hover:bg-black transition-all">Nueva Orden de Producción</button>
    </div>

    <!-- CONTENEDOR DE LOTES -->
    <div id="lotes-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Cargado por JS -->
    </div>

</div>