<div id="modal-lote" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3 class="text-2xl tracking-tighter">Nueva Orden</h3>
        <input type="text" id="lote-nombre" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none not-italic" placeholder="Nombre">
        <textarea id="lote-desc" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-medium h-32 outline-none not-italic font-sans" placeholder="Descripción..."></textarea>
        <div class="flex gap-4"><button onclick="cerrarModalLote()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] text-slate-400 tracking-widest">Cerrar</button><button onclick="guardarLote()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] shadow-xl tracking-widest">Crear</button></div>
    </div>
</div>

<div id="modal-modelo" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3 class="text-2xl tracking-tighter">Nuevo Modelo</h3>
        <div class="space-y-4 not-italic">
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Nombre del Modelo</label>
                <input type="text" id="mod-nombre" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none" placeholder="Ej: Huawei B311">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Etiqueta Principal</label>
                    <select id="mod-template-p" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none"></select>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Etiqueta Trasera (Opc)</label>
                    <select id="mod-template-s" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none appearance-none">
                        <option value="">Ninguna</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase text-slate-400 ml-4">Cant. Etiquetas Total</label>
                <input type="number" id="mod-cant" value="1" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none">
            </div>
        </div>
        <div class="flex gap-4"><button onclick="cerrarModalModelo()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest">Cerrar</button><button onclick="guardarModelo()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] tracking-widest shadow-xl">Guardar</button></div>
    </div>
</div>

<div id="modal-confirm" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[300] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-sm p-10 text-center space-y-6">
        <div class="h-20 w-20 bg-red-50 text-red-500 rounded-3xl flex items-center justify-center mx-auto text-2xl shadow-inner">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <h3 class="text-xl font-black uppercase italic tracking-tighter text-slate-900" id="confirm-title">¿Estás seguro?</h3>
            <p class="text-xs text-slate-400 font-bold uppercase mt-2 leading-relaxed" id="confirm-msg">Esta acción no se puede deshacer.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="cerrarConfirm()" class="flex-1 bg-slate-100 py-4 rounded-2xl text-[9px] font-black uppercase tracking-widest text-slate-400">Cancelar</button>
            <button id="confirm-btn-exec" class="flex-1 bg-red-500 text-white py-4 rounded-2xl text-[9px] font-black uppercase tracking-widest shadow-lg shadow-red-200">Eliminar</button>
        </div>
    </div>
</div>