let designItems = [], selectedIndices = [], currentDesignId = null;
const MM_TO_PX = 5.0;

// SISTEMA DE ESTADO PARA EL MOUSE
let isDragging = false;
let isResizing = false;
let currentHandle = null;
let startX, startY, startPositions = [];

async function abrirDesigner(id) {
    currentDesignId = id; 
    const tpl = window.templates.find(t => t.id == id);
    document.getElementById('design-template-name').textContent = tpl.nombre;
    
    document.getElementById('canvas-w-mm').value = tpl.ancho;
    document.getElementById('canvas-h-mm').value = tpl.alto;
    
    const canvas = document.getElementById('label-canvas');
    canvas.style.width = (tpl.ancho * MM_TO_PX) + 'px'; 
    canvas.style.height = (tpl.alto * MM_TO_PX) + 'px';
    
    try { 
        const parsed = tpl.config_json ? JSON.parse(tpl.config_json) : []; 
        designItems = Array.isArray(parsed) ? parsed : []; 
    } catch (e) { designItems = []; }
    
    selectedIndices = [];
    renderDesignItems(); 
    switchView('designer');
}

function updateCanvasSize() {
    const w = document.getElementById('canvas-w-mm').value;
    const h = document.getElementById('canvas-h-mm').value;
    const canvas = document.getElementById('label-canvas');
    canvas.style.width = (w * MM_TO_PX) + 'px';
    canvas.style.height = (h * MM_TO_PX) + 'px';
}

function renderDesignItems() {
    const canvas = document.getElementById('label-canvas'); 
    canvas.innerHTML = '';
    
    designItems.forEach((item, index) => {
        const isSelected = selectedIndices.includes(index);
        const el = document.createElement('div');
        el.className = `canvas-item item-${item.type} ${isSelected ? 'selected' : ''} ${item.fill ? 'item-filled' : ''}`;
        el.id = `item-${index}`;
        
        el.style.left = item.x + 'px'; 
        el.style.top = item.y + 'px';
        el.style.width = item.width + 'px';
        el.style.height = item.height + 'px';
        el.style.color = item.color || '#000000';
        el.style.zIndex = item.zIndex || index;
        
        if (['sn', 'ssid', 'pass', 'ssid2', 'ssid3', 'modelo', 'texto'].includes(item.type)) {
            el.style.overflow = 'hidden';
            el.style.display = 'flex';
            el.style.alignItems = 'center';
            el.style.justifyContent = 'center';
            el.style.textAlign = 'center';
            el.style.whiteSpace = 'nowrap';
            el.style.fontSize = (item.fontSize || 14) + 'px'; 
            el.style.fontWeight = item.bold ? 'bold' : 'normal';
            el.style.fontFamily = item.fontFamily || "'Plus Jakarta Sans'";
            el.textContent = item.sampleText;
            
            if (['sn', 'ssid', 'pass', 'ssid2', 'ssid3', 'modelo'].includes(item.type)) {
                el.classList.add('text-blue-600', 'font-black');
                if (item.type === 'ssid2') el.classList.replace('text-blue-600', 'text-indigo-600');
                if (item.type === 'ssid3') el.classList.replace('text-blue-600', 'text-purple-600');
            }
        } else if (item.type === 'barcode' || item.type === 'barcode_pass' || item.type === 'barcode_model') {
            const label = item.type === 'barcode' ? 'SN' : (item.type === 'barcode_pass' ? 'PASS' : 'MODEL');
            const color = item.type === 'barcode' ? 'blue' : (item.type === 'barcode_pass' ? 'slate' : 'amber');
            el.innerHTML = `<div class="flex flex-col items-center justify-center w-full h-full bg-white border border-${color}-200">
                <i class="fas fa-barcode text-2xl text-${color}-600"></i>
                <span class="text-[6px] font-black uppercase mt-1 text-${color}-600">CÓDIGO ${label}</span>
            </div>`;
        } else if (item.type === 'qr_pass') {
            el.innerHTML = `<div class="flex flex-col items-center justify-center w-full h-full bg-white border border-slate-200">
                <i class="fas fa-qrcode text-3xl text-slate-800"></i>
                <span class="text-[5px] font-black uppercase mt-1 text-slate-800">QR PASS</span>
            </div>`;
        } else if (item.type === 'image') {
            el.style.backgroundImage = `url(${item.src})`;
            el.style.backgroundSize = 'contain';
            el.style.backgroundRepeat = 'no-repeat';
            el.style.backgroundPosition = 'center';
        } else if (item.type === 'rect' || item.type === 'circle') {
            el.style.borderColor = item.color || '#000000';
            el.style.borderStyle = 'solid';
            el.style.borderWidth = (item.borderWidth || 2) + 'px';
            if (item.fill) el.style.backgroundColor = item.color || '#000000';
        } else if (item.type === 'line') {
            el.style.borderTop = `${item.borderWidth || 2}px solid ${item.color || '#000000'}`;
            el.style.height = '0';
        }

        if (isSelected && selectedIndices.length === 1 && !item.locked) {
            const handles = ['nw', 'ne', 'sw', 'se'];
            handles.forEach(h => {
                const div = document.createElement('div');
                div.className = `resizer-handle ${h}`;
                div.style.width = '8px'; div.style.height = '8px';
                div.style.background = '#2563eb'; div.style.position = 'absolute';
                div.style.cursor = h + '-resize'; div.style.zIndex = '1000';
                if (h.includes('n')) div.style.top = '-4px'; else div.style.bottom = '-4px';
                if (h.includes('w')) div.style.left = '-4px'; else div.style.right = '-4px';
                div.onmousedown = (e) => { e.stopPropagation(); initResize(e, index, h); };
                el.appendChild(div);
            });
        }

        el.onmousedown = (e) => { 
            if (e.ctrlKey || e.metaKey) {
                toggleSelect(index);
            } else {
                if (!selectedIndices.includes(index)) selectItem(index);
                if (!item.locked) initDrag(e); 
            }
            e.stopPropagation(); 
        };
        
        canvas.appendChild(el);
    });
}

function selectItem(index) {
    selectedIndices = [index];
    updatePropInputs();
    renderDesignItems();
}

function toggleSelect(index) {
    const i = selectedIndices.indexOf(index);
    if (i > -1) selectedIndices.splice(i, 1);
    else selectedIndices.push(index);
    updatePropInputs();
    renderDesignItems();
}

function initDrag(e) {
    if (e.button !== 0) return;
    isDragging = true;
    startX = e.clientX;
    startY = e.clientY;
    startPositions = selectedIndices.map(idx => ({
        index: idx,
        x: designItems[idx].x,
        y: designItems[idx].y
    }));
}

function initResize(e, index, handle) {
    isResizing = true;
    currentHandle = handle;
    const item = designItems[index];
    startX = e.clientX;
    startY = e.clientY;
    startPositions = [{
        index: index,
        x: item.x,
        y: item.y,
        w: item.width,
        h: item.height
    }];
}

window.addEventListener('mousemove', (e) => {
    if (!isDragging && !isResizing) return;
    const dx = e.clientX - startX;
    const dy = e.clientY - startY;

    if (isDragging) {
        startPositions.forEach(pos => {
            designItems[pos.index].x = pos.x + dx;
            designItems[pos.index].y = pos.y + dy;
        });
    }

    if (isResizing) {
        const pos = startPositions[0];
        const item = designItems[pos.index];
        if (currentHandle.includes('e')) item.width = Math.max(5, pos.w + dx);
        if (currentHandle.includes('s')) item.height = Math.max(5, pos.h + dy);
        if (currentHandle.includes('w')) {
            const newW = pos.w - dx;
            if (newW > 5) { item.width = newW; item.x = pos.x + dx; }
        }
        if (currentHandle.includes('n')) {
            const newH = pos.h - dy;
            if (newH > 5) { item.height = newH; item.y = pos.y + dy; }
        }
    }
    renderDesignItems();
    updatePropInputs();
});

window.addEventListener('mouseup', () => {
    isDragging = false;
    isResizing = false;
});

window.addEventListener('keydown', (e) => {
    if (selectedIndices.length === 0 || document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') return;
    
    const step = e.shiftKey ? 10 : 1;
    if (['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(e.key)) {
        e.preventDefault();
        selectedIndices.forEach(idx => {
            const item = designItems[idx];
            if (e.key === 'ArrowLeft') item.x -= step;
            if (e.key === 'ArrowRight') item.x += step;
            if (e.key === 'ArrowUp') item.y -= step;
            if (e.key === 'ArrowDown') item.y += step;
        });
    }
    
    if (e.key === 'Delete' || e.key === 'Backspace') removeSelectedItem();
    
    renderDesignItems();
    updatePropInputs();
});

function addItem(type, sampleText, w = 100, h = 30) {
    const newItem = { 
        type, sampleText, x: 20, y: 20, 
        width: w, height: h, 
        fontSize: 14, fontFamily: "'Plus Jakarta Sans'", 
        bold: false, color: '#000000', 
        fill: false, borderWidth: 1, 
        locked: false, zIndex: designItems.length 
    };
    designItems.push(newItem);
    selectItem(designItems.length - 1);
}

function handleImageUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
        const img = new Image();
        img.onload = () => {
            designItems.push({
                type: 'image', src: event.target.result,
                x: 20, y: 20, width: 100, height: (100 * img.height) / img.width,
                locked: false, zIndex: designItems.length
            });
            selectItem(designItems.length - 1);
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
}

function updatePropInputs() {
    const panel = document.getElementById('propiedades-panel');
    if (selectedIndices.length === 0) {
        panel.classList.add('hidden');
        return;
    }
    
    panel.classList.remove('hidden');
    const firstItem = designItems[selectedIndices[0]];
    const isText = ['sn', 'ssid', 'pass', 'ssid2', 'ssid3', 'modelo', 'texto'].includes(firstItem.type);
    
    document.getElementById('prop-text-only').style.display = isText ? 'block' : 'none';
    document.getElementById('prop-bold-box').style.display = isText ? 'flex' : 'none';
    document.getElementById('prop-fill-box').style.display = ['rect', 'circle'].includes(firstItem.type) ? 'flex' : 'none';
    document.getElementById('prop-size-box').style.display = isText ? 'block' : 'none';
    
    if (selectedIndices.length === 1) {
        document.getElementById('prop-content').value = firstItem.sampleText || '';
        document.getElementById('prop-size').value = Math.round(firstItem.width);
        document.getElementById('prop-height').value = Math.round(firstItem.height);
        document.getElementById('prop-color').value = firstItem.color || '#000000';
        document.getElementById('prop-font-size').value = firstItem.fontSize || 14;
        document.getElementById('prop-font-family').value = firstItem.fontFamily || "'Plus Jakarta Sans'";
        document.getElementById('prop-bold').checked = firstItem.bold;
        document.getElementById('prop-fill').checked = firstItem.fill;
        document.getElementById('prop-locked').checked = firstItem.locked;
        document.getElementById('lock-icon').className = firstItem.locked ? 'fas fa-lock text-blue-400' : 'fas fa-unlock';
        document.getElementById('bold-icon').className = firstItem.bold ? 'fas fa-bold text-blue-400' : 'fas fa-bold';
    }
}

function updateItemProp(prop, val) {
    selectedIndices.forEach(idx => {
        const item = designItems[idx];
        if (!item.locked || prop === 'locked') {
            item[prop] = val;
        }
    });
    renderDesignItems();
    updatePropInputs();
}

function alignItem(mode) {
    const canvas = document.getElementById('label-canvas');
    selectedIndices.forEach(idx => {
        const item = designItems[idx];
        if (mode === 'h') item.x = (parseInt(canvas.style.width) / 2) - (item.width / 2);
        if (mode === 'v') item.y = (parseInt(canvas.style.height) / 2) - (item.height / 2);
    });
    renderDesignItems();
    updatePropInputs();
}

function removeSelectedItem() {
    selectedIndices.sort((a, b) => b - a).forEach(idx => {
        designItems.splice(idx, 1);
    });
    selectedIndices = [];
    document.getElementById('propiedades-panel').classList.add('hidden');
    renderDesignItems();
}

function deselectAll() {
    selectedIndices = [];
    document.getElementById('propiedades-panel').classList.add('hidden');
    renderDesignItems();
}

async function guardarDiseno() {
    try {
        const w = document.getElementById('canvas-w-mm').value;
        const h = document.getElementById('canvas-h-mm').value;
        await fetch('api.php?controller=EtiquetaTemplates&action=guardar', {
            method: 'POST',
            body: JSON.stringify({ id: currentDesignId, ancho: w, alto: h })
        });
        const res = await fetch('api.php?controller=EtiquetaTemplates&action=guardar', {
            method: 'POST',
            body: JSON.stringify({ id: currentDesignId, config_json: JSON.stringify(designItems) })
        });
        if ((await res.json()).status === 'success') {
            showToast('Diseño Guardado', 'success');
            await fetchTemplates();
        }
    } catch (e) { showToast('Error al guardar', 'error'); }
}

function mostrarPreviewImpresion() {
    const w = document.getElementById('canvas-w-mm').value;
    const h = document.getElementById('canvas-h-mm').value;
    const container = document.getElementById('preview-print-label');
    container.style.width = w + 'mm'; container.style.height = h + 'mm';
    container.style.position = 'relative'; container.style.background = 'white';
    container.style.overflow = 'hidden'; container.innerHTML = '';

    designItems.forEach(item => {
        const el = document.createElement('div');
        el.style.position = 'absolute';
        el.style.left = (item.x / MM_TO_PX) + 'mm';
        el.style.top = (item.y / MM_TO_PX) + 'mm';
        el.style.width = (item.width / MM_TO_PX) + 'mm';
        el.style.height = (item.height / MM_TO_PX) + 'mm';
        el.style.color = item.color || '#000';
        el.style.zIndex = item.zIndex || 0;
        
        if (['sn', 'ssid', 'pass', 'ssid2', 'modelo', 'texto'].includes(item.type)) {
            el.style.fontSize = (item.fontSize / (MM_TO_PX/1.3)) + 'mm'; 
            el.style.fontWeight = item.bold ? 'bold' : 'normal';
            el.style.fontFamily = item.fontFamily || 'sans-serif';
            el.style.display = 'flex'; el.style.alignItems = 'center';
            el.style.justifyContent = 'center'; el.style.textAlign = 'center';
            el.textContent = item.sampleText;
        } else if (['barcode', 'barcode_pass', 'barcode_model'].includes(item.type)) {
            const label = item.type === 'barcode' ? 'SN' : (item.type === 'barcode_pass' ? 'PASS' : 'MODEL');
            el.style.background = '#000'; el.style.display = 'flex';
            el.style.flexDirection = 'column'; el.style.alignItems = 'center';
            el.style.justifyContent = 'flex-end';
            el.innerHTML = `<div style="width:90%; height:70%; background: repeating-linear-gradient(90deg, #fff, #fff 1px, #000 1px, #000 3px);"></div><span style="color:#fff; font-size:1.5mm; font-weight:bold; margin-bottom:0.5mm">${label}</span>`;
        } else if (item.type === 'qr_pass') {
            el.style.background = '#000'; el.style.display = 'flex';
            el.style.alignItems = 'center'; el.style.justifyContent = 'center';
            el.innerHTML = `<i class="fas fa-qrcode" style="color:white; font-size: 5mm;"></i>`;
        } else if (item.type === 'image') {
            el.style.backgroundImage = `url(${item.src})`;
            el.style.backgroundSize = 'contain'; el.style.backgroundRepeat = 'no-repeat';
            el.style.backgroundPosition = 'center';
        } else if (item.type === 'rect' || item.type === 'circle') {
            el.style.border = `${(item.borderWidth || 1) / (MM_TO_PX/1.5)}mm solid ${item.color || '#000'}`;
            if (item.fill) el.style.backgroundColor = item.color || '#000';
            if (item.type === 'circle') el.style.borderRadius = '50%';
        } else if (item.type === 'line') {
            el.style.height = '0';
            el.style.borderTop = `${(item.borderWidth || 1) / (MM_TO_PX/1.5)}mm solid ${item.color || '#000'}`;
        }
        container.appendChild(el);
    });
    document.getElementById('modal-preview-print').classList.remove('hidden');
}

function cerrarPreviewImpresion() { document.getElementById('modal-preview-print').classList.add('hidden'); }
function cerrarPreviewImpresion() { document.getElementById('modal-preview-print').classList.add('hidden'); }