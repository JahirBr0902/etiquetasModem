<div id="view-produccion" class="hidden grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div id="prod-form-card" class="lg:col-span-1 space-y-4">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm sticky top-28">
            <h3 class="font-black text-slate-800 mb-6 uppercase tracking-widest text-xs italic">Agregar Equipo</h3>
            <form id="modem-form" class="space-y-4">
                <select id="modelo_id" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none"></select>
                <input type="text" id="sn" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-mono font-bold outline-none uppercase" placeholder="Num Serie">
                <input type="text" id="password" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-mono font-bold outline-none" placeholder="Wifi Pass">
                <button type="submit" class="w-full bg-slate-900 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-black transition-all uppercase tracking-widest text-[10px]">Guardar</button>
            </form>
        </div>
    </div>
    <div class="lg:col-span-3 space-y-4">
        <div id="lote-action-header" class="bg-blue-600 p-8 rounded-[2.5rem] text-white flex justify-between items-center shadow-xl italic font-black uppercase">
            <div><h4 id="prod-lote-name" class="text-2xl tracking-tight">---</h4><p id="prod-lote-desc" class="text-[10px] text-blue-100 mt-1">---</p></div>
            <div id="lote-dynamic-actions" class="flex gap-3"></div>
        </div>
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                    <tr><th class="px-8 py-6">Equipo</th><th class="px-8 py-6">Estado</th><th class="px-8 py-6 text-right">Imprimir</th></tr>
                </thead>
                <tbody id="modems-table-body" class="divide-y divide-slate-100 font-bold"></tbody>
            </table>
        </div>
    </div>
</div>