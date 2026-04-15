<aside class="w-64 bg-white border-r border-slate-200 flex flex-col sticky top-0 h-screen z-40 uppercase italic font-black">
    <div class="p-6 flex items-center gap-3">
        <div class="bg-blue-600 p-2 rounded-xl text-white shadow-lg"><i class="fas fa-microchip"></i></div>
        <h1 class="font-extrabold text-xl tracking-tight text-slate-900">Witmac <span class="text-blue-600">PRO</span></h1>
    </div>
    <nav class="flex-1 px-4 space-y-1 mt-4">
        <button onclick="switchView('dashboard')" id="nav-dashboard" class="sidebar-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 active"><i class="fas fa-th-large w-5"></i> Dashboard</button>
        <button onclick="switchView('inventario')" id="nav-inventario" class="sidebar-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50"><i class="fas fa-warehouse w-5"></i> Inventario</button>
        <button onclick="switchView('produccion')" id="nav-produccion" class="sidebar-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 hidden"><i class="fas fa-industry w-5"></i> Producción</button>
        <div class="pt-6 pb-2 px-4 text-[10px] font-black text-slate-400 tracking-widest">Ajustes</div>
        <button onclick="switchView('modelos')" id="nav-modelos" class="sidebar-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50"><i class="fas fa-boxes w-5"></i> Modelos</button>
        <button onclick="switchView('templates')" id="nav-templates" class="sidebar-item w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50"><i class="fas fa-layer-group w-5"></i> Templates</button>
    </nav>
</aside>