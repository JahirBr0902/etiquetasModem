<div id="view-designer" class="hidden fixed inset-0 bg-slate-900 z-[100] flex flex-col font-sans select-none">
    <header class="bg-slate-900 border-b border-white/5 px-8 py-4 flex justify-between items-center font-black italic uppercase">
        <div class="flex items-center gap-6">
            <button onclick="switchView('templates')" class="h-10 w-10 bg-white/5 rounded-xl text-white/50 hover:text-white transition-all flex items-center justify-center"><i class="fas fa-chevron-left"></i></button>
            <div>
                <h2 class="text-white text-lg uppercase tracking-tighter leading-none flex items-center gap-2">Diseñador: <input type="text" id="design-template-name" class="bg-transparent text-blue-400 outline-none border-b border-transparent focus:border-blue-400/30 transition-all w-64 px-1" value="---"></h2>
                <p class="text-[9px] text-white/30 tracking-widest mt-1">Plantilla Base para Producción</p>
            </div>
        </div>
        <div class="flex gap-3 not-italic">
            <div class="flex bg-white/5 rounded-xl p-1 border border-white/5 mr-4">
                <div class="px-3 py-2 flex flex-col justify-center">
                    <span class="text-[8px] text-white/40 uppercase font-black">Lienzo (mm)</span>
                    <div class="flex items-center gap-2">
                        <input type="number" id="canvas-w-mm" oninput="updateCanvasSize()" class="w-12 bg-transparent text-white text-xs font-bold outline-none" value="50">
                        <span class="text-white/20">×</span>
                        <input type="number" id="canvas-h-mm" oninput="updateCanvasSize()" class="w-12 bg-transparent text-white text-xs font-bold outline-none" value="25">
                    </div>
                </div>
            </div>
            <button onclick="mostrarPreviewImpresion()" class="bg-white/10 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-white/20 transition-all flex items-center gap-2"><i class="fas fa-eye"></i> Preview</button>
            <button onclick="guardarDiseno()" class="bg-blue-600 text-white px-10 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-blue-500 shadow-xl transition-all flex items-center gap-2"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </header>
    
    <div class="flex-1 flex overflow-hidden text-white">
        <!-- BARRA LATERAL IZQUIERDA: HERRAMIENTAS -->
        <aside class="w-72 bg-slate-800 border-r border-white/5 flex flex-col overflow-hidden">
            <div class="flex-1 overflow-y-auto p-6 space-y-8 font-black uppercase text-[10px]">
                
                <!-- VARIABLES DINÁMICAS -->
                <div class="space-y-4">
                    <h4 class="text-white/30 tracking-widest flex items-center gap-2"><i class="fas fa-database text-[8px]"></i> Datos Dinámicos</h4>
                    <div class="grid grid-cols-2 gap-2 text-white">
                        <button onclick="addItem('sn', 'SN: 12345678', 120, 30)" class="p-3 bg-blue-600/10 border border-blue-500/20 rounded-xl hover:bg-blue-600/20 transition-all text-left">
                            <span class="block text-blue-400 mb-1">SN</span>
                            <span class="text-[7px] text-blue-400/50 normal-case">Serie</span>
                        </button>
                        <button onclick="addItem('barcode', '', 150, 60)" class="p-3 bg-blue-600/10 border border-blue-500/20 rounded-xl hover:bg-blue-600/20 transition-all text-left">
                            <span class="block text-blue-400 mb-1 font-black">CÓDIGO SN</span>
                            <span class="text-[7px] text-blue-400/50 normal-case">Barras (Serie)</span>
                        </button>
                        <button onclick="addItem('barcode_pass', '', 150, 60)" class="p-3 bg-slate-600/10 border border-slate-500/20 rounded-xl hover:bg-slate-600/20 transition-all text-left">
                            <span class="block text-slate-300 mb-1 font-black">CÓDIGO PASS</span>
                            <span class="text-[7px] text-slate-300/50 normal-case">Barras (Contraseña)</span>
                        </button>
                        <button onclick="addItem('barcode_model', '', 150, 60)" class="p-3 bg-amber-600/10 border border-amber-500/20 rounded-xl hover:bg-amber-600/20 transition-all text-left">
                            <span class="block text-amber-400 mb-1 font-black">CÓDIGO MODELO</span>
                            <span class="text-[7px] text-amber-400/50 normal-case">Barras (Modelo)</span>
                        </button>
                        <button onclick="addItem('qr_pass', '', 80, 80)" class="p-3 bg-slate-600/10 border border-slate-500/20 rounded-xl hover:bg-slate-600/20 transition-all text-left">
                            <span class="block text-slate-300 mb-1 font-black">QR PASS</span>
                            <span class="text-[7px] text-slate-300/50 normal-case">QR (Contraseña)</span>
                        </button>
                        <button onclick="addItem('qr_wifi', '', 100, 100)" class="p-3 bg-green-600/10 border border-green-500/20 rounded-xl hover:bg-green-600/20 transition-all text-left">
                            <span class="block text-green-400 mb-1 font-black">QR WIFI AUTO</span>
                            <span class="text-[7px] text-green-400/50 normal-case">Conecta directo</span>
                        </button>
                        <button onclick="addItem('ssid', 'SSID: Witmac', 120, 25)" class="p-3 bg-blue-600/10 border border-blue-500/20 rounded-xl hover:bg-blue-600/20 transition-all text-left">
                            <span class="block text-blue-400 mb-1">SSID PRINCIPAL</span>
                        </button>
                        <button onclick="addItem('ssid2', 'SSID: Witmac_2.4G', 120, 25)" class="p-3 bg-indigo-600/10 border border-indigo-500/20 rounded-xl hover:bg-indigo-600/20 transition-all text-left">
                            <span class="block text-indigo-400 mb-1">SSID 2.4G</span>
                        </button>
                        <button onclick="addItem('ssid3', 'SSID: Witmac_5G', 120, 25)" class="p-3 bg-purple-600/10 border border-purple-500/20 rounded-xl hover:bg-purple-600/20 transition-all text-left">
                            <span class="block text-purple-400 mb-1">SSID 5G</span>
                        </button>
                        <button onclick="addItem('pass', 'P: 12345678', 120, 25)" class="p-3 bg-slate-600/10 border border-slate-500/20 rounded-xl hover:bg-slate-600/20 transition-all text-left">
                            <span class="block text-slate-300 mb-1">WIFI PASS</span>
                        </button>
                    </div>
                </div>

                <!-- ELEMENTOS ESTÁTICOS -->
                <div class="space-y-4 pt-4 border-t border-white/5">
                    <h4 class="text-white/30 tracking-widest flex items-center gap-2"><i class="fas fa-shapes text-[8px]"></i> Elementos Fijos</h4>
                    <div class="space-y-2">
                        <button onclick="addItem('texto', 'TEXTO FIJO', 100, 30)" class="w-full p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all flex items-center gap-3">
                            <i class="fas fa-font text-blue-400"></i> Texto Estático
                        </button>
                        <button onclick="document.getElementById('image-upload').click()" class="w-full p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 transition-all flex items-center gap-3">
                            <i class="fas fa-image text-green-400"></i> Insertar Imagen
                        </button>
                        <input type="file" id="image-upload" class="hidden" accept="image/*" onchange="handleImageUpload(event)">
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 pt-2">
                        <button onclick="addItem('rect', '', 80, 50)" class="p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 flex flex-col items-center gap-2">
                            <i class="fas fa-square text-lg"></i>
                            <span class="text-[7px]">Rect</span>
                        </button>
                        <button onclick="addItem('circle', '', 50, 50)" class="p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 flex flex-col items-center gap-2">
                            <i class="fas fa-circle text-lg"></i>
                            <span class="text-[7px]">Círc</span>
                        </button>
                        <button onclick="addItem('line', '', 100, 2)" class="p-3 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 flex flex-col items-center gap-2">
                            <i class="fas fa-minus text-lg"></i>
                            <span class="text-[7px]">Línea</span>
                        </button>
                    </div>
                </div>

                <!-- PROPIEDADES (CONTEXTUAL) -->
                <div id="propiedades-panel" class="hidden space-y-5 pt-6 border-t border-white/5 font-sans">
                    <div class="flex justify-between items-center">
                        <h4 class="text-blue-400 tracking-widest uppercase text-[9px] font-black">Propiedades</h4>
                        <div class="flex gap-1">
                            <button onclick="alignItem('h')" class="w-7 h-7 bg-white/5 rounded-lg flex items-center justify-center hover:bg-white/10" title="Centrar H"><i class="fas fa-arrows-alt-h text-[10px]"></i></button>
                            <button onclick="alignItem('v')" class="w-7 h-7 bg-white/5 rounded-lg flex items-center justify-center hover:bg-white/10" title="Centrar V"><i class="fas fa-arrows-alt-v text-[10px]"></i></button>
                            <button onclick="removeSelectedItem()" class="w-7 h-7 bg-red-500/10 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500/20" title="Eliminar"><i class="fas fa-trash text-[10px]"></i></button>
                        </div>
                    </div>

                    <div id="prop-text-only" class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[8px] text-white/30 uppercase font-bold">Contenido</label>
                            <textarea id="prop-content" oninput="updateItemProp('sampleText', this.value)" class="w-full bg-slate-900 border border-white/10 rounded-xl p-3 text-white text-xs outline-none focus:border-blue-500/50 resize-none" rows="2"></textarea>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-[8px] text-white/30 uppercase font-bold">Tipografía</label>
                            <select id="prop-font-family" onchange="updateItemProp('fontFamily', this.value)" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-white text-[10px] outline-none font-bold">
                                <option value="'Plus Jakarta Sans'">Plus Jakarta (Normal)</option>
                                <option value="'JetBrains Mono'">JetBrains Mono (Código)</option>
                                <option value="'Roboto Condensed'">Roboto Condensed (Compacta)</option>
                                <option value="'Arial'">Arial (Estándar)</option>
                                <option value="'Times New Roman'">Times New Roman (Serif)</option>
                                <option value="'Courier New'">Courier New (Máquina)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[8px] text-white/30 uppercase font-bold">Ancho (px)</label>
                            <input type="number" id="prop-size" oninput="updateItemProp('width', parseInt(this.value))" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-white text-xs outline-none focus:border-blue-500/50">
                        </div>
                        <div class="space-y-1" id="box-height">
                            <label class="text-[8px] text-white/30 uppercase font-bold">Alto (px)</label>
                            <input type="number" id="prop-height" oninput="updateItemProp('height', parseInt(this.value))" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-white text-xs outline-none focus:border-blue-500/50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-[8px] text-white/30 uppercase font-bold">Color / Borde</label>
                            <div class="flex items-center bg-slate-900 border border-white/10 rounded-lg overflow-hidden h-9 px-2">
                                <input type="color" id="prop-color" oninput="updateItemProp('color', this.value)" class="w-full h-6 bg-transparent border-none cursor-pointer">
                            </div>
                        </div>
                        <div id="prop-size-box" class="space-y-1">
                            <label class="text-[8px] text-white/30 uppercase font-bold">Tam. Fuente</label>
                            <input type="number" id="prop-font-size" oninput="updateItemProp('fontSize', parseInt(this.value))" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-white text-xs outline-none focus:border-blue-500/50">
                        </div>
                    </div>

                    <div id="prop-wifi-band" class="space-y-2 hidden">
                        <label class="text-[8px] text-white/30 uppercase font-bold">Red WiFi (SSID)</label>
                        <select id="prop-wifi-select" onchange="updateItemProp('wifiBand', this.value)" class="w-full bg-slate-900 border border-white/10 rounded-lg p-2 text-white text-[10px] outline-none font-bold">
                            <option value="normal">SSID Principal</option>
                            <option value="2.4g">SSID 2.4G (_2.4)</option>
                            <option value="5g">SSID 5G (_5G)</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <label id="prop-bold-box" class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                            <input type="checkbox" id="prop-bold" onchange="updateItemProp('bold', this.checked)" class="hidden">
                            <i class="fas fa-bold text-[10px]" id="bold-icon"></i>
                            <span class="text-[9px] uppercase font-bold">Negrita</span>
                        </label>
                        <label id="prop-fill-box" class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                            <input type="checkbox" id="prop-fill" onchange="updateItemProp('fill', this.checked)" class="hidden">
                            <i class="fas fa-fill-drip text-[10px]" id="fill-icon"></i>
                            <span class="text-[9px] uppercase font-bold">Relleno</span>
                        </label>
                        <label class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-lg border border-white/10 cursor-pointer hover:bg-white/10 transition-all">
                            <input type="checkbox" id="prop-locked" onchange="updateItemProp('locked', this.checked)" class="hidden">
                            <i class="fas fa-lock text-[10px]" id="lock-icon"></i>
                            <span class="text-[9px] uppercase font-bold">Fijar</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- TECLAS DE ATAJO -->
            <div class="p-6 bg-slate-900/50 border-t border-white/5">
                <div class="flex flex-wrap gap-2">
                    <span class="text-[7px] text-white/20 bg-white/5 px-2 py-1 rounded border border-white/5 uppercase">Supr: Eliminar</span>
                    <span class="text-[7px] text-white/20 bg-white/5 px-2 py-1 rounded border border-white/5 uppercase">Flechas: Mover</span>
                </div>
            </div>
        </aside>
        
        <!-- AREA CENTRAL: CANVA -->
        <div class="flex-1 bg-slate-950 flex items-center justify-center p-12 overflow-auto custom-scrollbar" onclick="deselectAll()">
            <div class="relative">
                <!-- REGLAS / GUÍAS OPCIONALES (PUDIERAN IR AQUÍ) -->
                <div id="label-canvas" class="label-canvas shadow-[0_0_100px_rgba(0,0,0,0.5)]" onclick="event.stopPropagation()"></div>
            </div>
        </div>
    </div>
</div>

<div id="modal-preview-print" class="fixed inset-0 z-[200] hidden flex items-center justify-center preview-overlay p-10 font-sans">
    <div class="relative flex flex-col items-center gap-8 max-w-full">
        <div class="bg-white p-8 rounded-[3rem] shadow-2xl flex flex-col items-center border border-white/10">
            <div class="text-[10px] font-black text-slate-400 mb-6 uppercase tracking-[0.4em] italic">Resultado de Impresión</div>
            <div class="border border-slate-100 p-2 bg-slate-50 rounded-xl">
                <div id="preview-print-label" class="preview-label-container shadow-sm"></div>
            </div>
            <p class="text-[9px] text-slate-400 mt-6 font-bold uppercase">Escala Real 1:1</p>
        </div>
        <button onclick="cerrarPreviewImpresion()" class="bg-white text-slate-900 px-12 py-4 rounded-full font-black text-[10px] uppercase shadow-2xl hover:scale-105 transition-all">Cerrar Vista Previa</button>
    </div>
</div>