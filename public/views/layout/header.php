<header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-5 sticky top-0 z-30 flex justify-between items-center italic font-black uppercase">
    <div>
        <h2 id="view-title" class="text-xl font-black text-slate-900 tracking-tight">Dashboard</h2>
        <p id="view-subtitle" class="text-[9px] text-slate-400 tracking-widest">Órdenes activas</p>
    </div>
    <div class="flex items-center gap-4">
        <div id="lote-status-badge" class="hidden px-4 py-1.5 rounded-full text-[10px] tracking-widest"></div>
        <button onclick="abrirModalLote()" id="btn-nuevo-lote" class="bg-blue-600 text-white px-6 py-3 rounded-2xl text-[10px] shadow-xl hover:bg-blue-700 transition-all"><i class="fas fa-plus mr-2"></i> Nueva Orden</button>
    </div>
</header>