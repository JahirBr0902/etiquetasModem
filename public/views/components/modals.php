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
        <h3>Nuevo Modelo</h3>
        <input type="text" id="mod-nombre" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl font-bold outline-none not-italic" placeholder="Nombre">
        <input type="number" id="mod-cant" value="1" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl outline-none not-italic font-bold">
        <div class="flex gap-4"><button onclick="cerrarModalModelo()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest">Cerrar</button><button onclick="guardarModelo()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] tracking-widest shadow-xl">Guardar</button></div>
    </div>
</div>

<div id="modal-template" class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md p-10 space-y-6 italic font-black uppercase">
        <h3>Nuevo Formato</h3>
        <input type="text" id="tpl-nombre" class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-2xl outline-none not-italic" placeholder="Nombre">
        <div class="grid grid-cols-2 gap-4 font-bold not-italic">
            <input type="number" id="tpl-ancho" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl" value="50">
            <input type="number" id="tpl-alto" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl" value="25">
        </div>
        <div class="flex gap-4"><button onclick="cerrarModalTemplate()" class="flex-1 bg-slate-100 py-5 rounded-2xl text-[10px] tracking-widest">Cerrar</button><button onclick="guardarTemplate()" class="flex-1 bg-blue-600 text-white py-5 rounded-2xl text-[10px] tracking-widest">Crear</button></div>
    </div>
</div>