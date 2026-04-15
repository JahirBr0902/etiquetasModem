<div id="view-produccion" class="hidden grid grid-cols-1 lg:grid-cols-4 gap-8">
    <div id="prod-form-card" class="lg:col-span-1 space-y-4">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm sticky top-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black italic uppercase tracking-tighter text-slate-800">Asignar Equipo</h3>
                <button onclick="cancelarEdicionModem()" id="btn-cancel-edit" class="hidden text-slate-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
            </div>
            <form id="modem-form" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">Número de Serie (SN)</label>
                    <div class="relative">
                        <i class="fas fa-barcode absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                        <input type="text" id="sn" class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-mono font-bold outline-none focus:border-blue-600 transition-all uppercase" placeholder="Escanear SN..." required>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">Modelo de Equipo</label>
                    <select id="modelo_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none" required></select>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">SSID / Red WiFi</label>
                        <input type="text" id="ssid" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none" placeholder="WITMAC_XXXX">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[9px] font-black uppercase text-slate-400 ml-4 italic">WiFi Password</label>
                        <input type="text" id="password" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl font-mono font-bold outline-none" placeholder="Password">
                    </div>
                </div>

                <button type="submit" id="btn-submit-modem" class="w-full bg-slate-900 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-slate-200 mt-4 hover:scale-[1.02] transition-all">Guardar Equipo</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-3 space-y-6">
        <div id="lote-action-header" class="bg-blue-600 p-10 rounded-[3rem] text-white flex justify-between items-center shadow-2xl shadow-blue-200 italic font-black uppercase overflow-hidden relative">
            <div class="relative z-10">
                <h2 id="prod-lote-name" class="text-4xl tracking-tighter">Cargando Lote...</h2>
                <p id="prod-lote-desc" class="text-[10px] opacity-60 tracking-[0.3em] mt-2">---</p>
            </div>
            <div id="lote-dynamic-actions" class="relative z-10 flex gap-3"></div>
            <i class="fas fa-boxes absolute -right-10 -bottom-10 text-[12rem] opacity-10 rotate-12"></i>
        </div>

        <div class="bg-white rounded-[3rem] shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">
                    <tr>
                        <th class="px-8 py-6 text-blue-600">SN / Serie</th>
                        <th class="px-8 py-6">Modelo</th>
                        <th class="px-8 py-6">Acceso WiFi</th>
                        <th class="px-8 py-6 text-center">Estado</th>
                        <th class="px-8 py-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody id="modems-table-body" class="divide-y divide-slate-100 font-bold"></tbody>
            </table>
        </div>
    </div>
</div>
