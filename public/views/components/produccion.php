<div id="view-produccion" class="hidden grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div id="prod-form-card" class="lg:col-span-1 space-y-4">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm sticky top-28">
            <h3 class="font-black text-slate-800 mb-6 uppercase tracking-widest text-xs italic">Agregar Equipo</h3>
            <form id="modem-form" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">Modelo de Equipo</label>
                    <select id="modelo_id" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none"></select>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">Número de Serie</label>
                    <input type="text" id="sn" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-mono font-bold outline-none uppercase" placeholder="Num Serie">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">SSID Red WiFi</label>
                        <input type="text" id="ssid" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none" placeholder="Auto">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">WiFi Password</label>
                        <input type="text" id="password" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-mono font-bold outline-none" placeholder="Password">
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" id="btn-cancel-edit" onclick="cancelarEdicionModem()" class="hidden flex-1 bg-slate-100 text-slate-400 font-black py-5 rounded-2xl transition-all uppercase tracking-widest text-[10px]">Cancelar</button>
                    <button type="submit" id="btn-submit-modem" class="flex-[2] bg-slate-900 text-white font-black py-5 rounded-2xl shadow-xl hover:bg-black transition-all uppercase tracking-widest text-[10px]">Guardar Equipo</button>
                </div>
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
                    <tr>
                        <th class="px-8 py-6">Equipo / Serie</th>
                        <th class="px-8 py-6">Modelo</th>
                        <th class="px-8 py-6">Acceso WiFi</th>
                        <th class="px-8 py-6">Estado</th>
                        <th class="px-8 py-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="modems-table-body" class="divide-y divide-slate-100 font-bold"></tbody>
            </table>
        </div>
    </div>
</div>