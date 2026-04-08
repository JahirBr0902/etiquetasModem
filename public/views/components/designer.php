<div id="view-designer" class="hidden fixed inset-0 bg-slate-900 z-[100] flex flex-col font-sans">
    <header class="bg-slate-900 border-b border-white/5 px-8 py-5 flex justify-between items-center font-black italic uppercase">
        <div class="flex items-center gap-6">
            <button onclick="switchView('templates')" class="h-12 w-12 bg-white/5 rounded-2xl text-white/50 hover:text-white transition-all flex items-center justify-center"><i class="fas fa-chevron-left"></i></button>
            <div><h2 class="text-white text-xl uppercase">Diseñador: <span id="design-template-name" class="text-blue-400">---</span></h2><p class="text-[10px] text-white/30 tracking-widest">Editor de Formato</p></div>
        </div>
        <div class="flex gap-3 not-italic">
            <button onclick="mostrarPreviewImpresion()" class="bg-white/10 text-white px-6 py-4 rounded-2xl font-black text-[10px] uppercase hover:bg-white/20 transition-all"><i class="fas fa-eye mr-2"></i> Preview</button>
            <button onclick="guardarDiseno()" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase hover:bg-blue-500 shadow-xl transition-all"><i class="fas fa-save mr-2"></i> Guardar</button>
        </div>
    </header>
    
    <div class="flex-1 flex overflow-hidden">
        <aside class="w-80 bg-slate-800 border-r border-white/5 p-8 space-y-8 overflow-y-auto font-black uppercase text-[10px]">
            <div class="space-y-4">
                <h4 class="text-white/30 tracking-widest">Variables</h4>
                <div class="grid grid-cols-2 gap-2 text-white">
                    <button onclick="addItem('sn', 'SN: 84729104')" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all">SN</button>
                    <button onclick="addItem('ssid', 'SSID: Witmac1234')" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all">SSID</button>
                    <button onclick="addItem('pass', 'P: Witmac2024*')" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all">PASS</button>
                    <button onclick="addItem('modelo', 'Modelo Modem')" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all">MOD</button>
                    <button onclick="addItem('texto', 'Texto Libre')" class="col-span-2 p-4 bg-blue-600/20 border border-blue-500/30 rounded-2xl text-blue-400 hover:bg-blue-600/30 transition-all">Texto Libre</button>
                </div>
            </div>
            <div class="space-y-4 pt-4 border-t border-white/5">
                <h4 class="text-white/30 tracking-widest">Gráficos</h4>
                <div class="grid grid-cols-3 gap-2 text-white/80">
                    <button onclick="addItem('rect', '', 60, 40)" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10"><i class="fas fa-square"></i></button>
                    <button onclick="addItem('circle', '', 40, 40)" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10"><i class="fas fa-circle"></i></button>
                    <button onclick="addItem('line', '', 80, 2)" class="p-4 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div id="propiedades-panel" class="hidden space-y-6 pt-8 border-t border-white/5 font-sans">
                <h4 class="text-blue-400 tracking-widest uppercase text-[10px]">Ajustes Objeto</h4>
                <div id="prop-text-only" class="space-y-4">
                    <input type="text" id="prop-content" oninput="updateItemProp('sampleText', this.value)" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white text-xs outline-none">
                    <select id="prop-font" onchange="updateItemProp('fontFamily', this.value)" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white text-xs outline-none">
                        <option value="'Plus Jakarta Sans'">Sans</option><option value="'JetBrains Mono'">Mono</option><option value="Arial">Arial</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" id="prop-size" oninput="updateItemProp('width', this.value)" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white text-xs outline-none">
                    <div id="box-height"><input type="number" id="prop-height" oninput="updateItemProp('height', this.value)" class="w-full bg-white/5 border border-white/10 rounded-xl p-3 text-white text-xs outline-none"></div>
                </div>
                <div class="flex gap-2">
                    <input type="color" id="prop-color" oninput="updateItemProp('color', this.value)" class="flex-1 h-10 bg-transparent border-none">
                    <div id="prop-fill-box" class="flex flex-col items-center"><input type="checkbox" id="prop-fill" onchange="updateItemProp('fill', this.checked)"><span class="text-[8px] text-white/40">FILL</span></div>
                </div>
                <div id="prop-bold-box" class="flex items-center gap-2"><input type="checkbox" id="prop-bold" onchange="updateItemProp('bold', this.checked)"><span class="text-xs text-white/70 font-black uppercase">BOLD</span></div>
                <button onclick="removeSelectedItem()" class="w-full bg-red-500/10 text-red-500 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">Eliminar</button>
            </div>
        </aside>
        <div class="flex-1 bg-slate-950 flex items-center justify-center p-20 overflow-auto" onclick="deselectAll()">
            <div id="label-canvas" class="label-canvas" onclick="event.stopPropagation()"></div>
        </div>
    </div>
</div>

<div id="modal-preview-print" class="fixed inset-0 z-[200] hidden flex items-center justify-center preview-overlay p-10 font-sans">
    <div class="relative flex flex-col items-center gap-10">
        <div class="bg-white p-10 rounded-3xl shadow-2xl flex flex-col items-center">
            <div class="text-[10px] font-black text-slate-400 mb-6 uppercase tracking-[0.3em]">Vista Real de Impresión</div>
            <div id="preview-print-label" class="preview-label-container shadow-2xl"></div>
        </div>
        <button onclick="cerrarPreviewImpresion()" class="bg-white text-slate-900 px-10 py-4 rounded-full font-black text-[10px] uppercase shadow-2xl hover:scale-105 transition-all">Cerrar Preview</button>
    </div>
</div>