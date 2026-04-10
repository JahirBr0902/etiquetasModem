<div id="view-impresion" class="hidden space-y-8">
    <!-- Header de Lote en Impresión -->
    <div class="bg-indigo-600 p-10 rounded-[3rem] text-white flex justify-between items-center shadow-2xl italic font-black uppercase overflow-hidden relative">
        <div class="relative z-10">
            <p class="text-indigo-200 text-[10px] tracking-[0.2em] mb-2">Departamento de Impresión</p>
            <h4 id="imp-lote-name" class="text-4xl tracking-tighter">---</h4>
            <div class="flex gap-4 mt-4">
                <span id="imp-lote-count" class="bg-white/20 px-4 py-1.5 rounded-full text-[9px]">0 EQUIPOS</span>
                <span id="imp-lote-status" class="bg-indigo-400 px-4 py-1.5 rounded-full text-[9px]">REVISIÓN</span>
            </div>
        </div>
        <div class="flex gap-4 relative z-10">
            <button onclick="regresarEstadoLote()" class="bg-indigo-700/50 text-white w-14 h-14 rounded-2xl flex items-center justify-center hover:bg-red-500 transition-all border border-indigo-500/30 group" title="Regresar a Producción">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </button>
            <button onclick="imprimirLote()" class="bg-white text-indigo-600 px-10 py-5 rounded-[2rem] text-xs font-black uppercase tracking-widest shadow-xl hover:scale-105 transition-all flex items-center gap-3">
                <i class="fas fa-print"></i> Imprimir Lote Completo
            </button>
            <button onclick="cambiarEstadoLote('IMPRESO')" class="bg-indigo-400/30 border border-indigo-400/50 text-white px-8 py-5 rounded-[2rem] text-xs font-black uppercase tracking-widest hover:bg-indigo-400/50 transition-all">
                Marcar como Finalizado
            </button>
        </div>
        <!-- Decoración de fondo -->
        <i class="fas fa-print absolute -right-10 -bottom-10 text-[15rem] text-white/5 -rotate-12"></i>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Resumen de Modelos -->
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm sticky top-28">
                <h3 class="font-black text-slate-800 mb-6 uppercase tracking-widest text-xs italic">Resumen de Etiquetas</h3>
                <div id="imp-resumen-modelos" class="space-y-3">
                    <!-- Se llena dinámicamente -->
                </div>
            </div>
        </div>

        <!-- Lista de Equipos -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                        <tr>
                            <th class="px-8 py-6">Num. Serie</th>
                            <th class="px-8 py-6">Modelo</th>
                            <th class="px-8 py-6">Configuración WiFi</th>
                            <th class="px-8 py-6 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="imp-modems-table-body" class="divide-y divide-slate-100 font-bold"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>