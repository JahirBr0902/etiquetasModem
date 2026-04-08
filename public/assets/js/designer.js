let designItems = [], selectedItemIndex = null, currentDesignId = null;
const MM_TO_PX = 5.0;

async function abrirDiseñador(id) {
    currentDesignId = id; 
    const tpl = window.templates.find(t => t.id == id);
    document.getElementById('design-template-name').textContent = tpl.nombre;
    
    const canvas = document.getElementById('label-canvas');
    canvas.style.width = (tpl.ancho * MM_TO_PX) + 'px'; 
    canvas.style.height = (tpl.alto * MM_TO_PX) + 'px';
    
    try { 
        const parsed = tpl.config_json ? JSON.parse(tpl.config_json) : []; 
        designItems = Array.isArray(parsed) ? parsed : []; 
    } catch (e) { designItems = []; }
    
    renderDesignItems(); 
    switchView('designer');
}

function renderDesignItems() {
    const canvas = document.getElementById('label-canvas'); 
    canvas.innerHTML = '';
    designItems.forEach((item, index) => {
        const el = document.createElement('div');
        el.className = `canvas-item item-${item.type} ${selectedItemIndex === index ? 'selected' : ''} ${item.fill ? 'item-filled' : ''}`;
        el.style.left = item.x + 'px'; 
        el.style.top = item.y + 'px';
        el.style.color = item.color || '#000000';
        
        if (['sn', 'ssid', 'pass', 'modelo', 'texto'].includes(item.type)) {
            el.style.fontSize = item.fontSize + 'px'; 
            el.style.fontWeight = item.bold ? 'bold' : 'normal';
            el.style.fontFamily = item.fontFamily || 'inherit';
            el.textContent = item.sampleText;
        } else {
            el.style.width = item.width + 'px'; 
            el.style.height = item.height + 'px';
            el.style.borderWidth = (item.borderWidth || 2) + 'px';
        }
        el.onmousedown = (e) => startDrag(e, index);
        el.onclick = (e) => { e.stopPropagation(); selectItem(index); };
        canvas.appendChild(el);
    });
}

function addItem(type, sampleText, w = 80, h = 40) {
    designItems.push({ 
        type, sampleText, x: 20, y: 20, fontSize: 14, 
        fontFamily: "'Plus Jakarta Sans'", bold: false, 
        color: '#000000', width: w, height: h, fill: false, borderWidth: 2 
    });
    selectItem(designItems.length - 1); 
    renderDesignItems();
}

function selectItem(index) {
    selectedItemIndex = index; 
    const item = designItems[index];
    document.getElementById('propiedades-panel').classList.remove('hidden');
    const isText = ['sn', 'ssid', 'pass', 'modelo', 'texto'].includes(item.type);
    
    document.getElementById('prop-text-only').style.display = isText ? 'block' : 'none';
    document.getElementById('prop-bold-box').style.display = isText ? 'flex' : 'none';
    document.getElementById('prop-fill-box').style.display = !isText && item.type !== 'line' ? 'flex' : 'none';
    document.getElementById('box-height').style.display = item.type === 'line' ? 'none' : 'block';
    
    document.getElementById('prop-content').value = item.sampleText || '';
    document.getElementById('prop-font').value = item.fontFamily || "'Plus Jakarta Sans'";
    document.getElementById('prop-size').value = isText ? item.fontSize : item.width;
    document.getElementById('prop-height').value = item.height || 0;
    document.getElementById('prop-color').value = item.color || '#000000';
    document.getElementById('prop-bold').checked = item.bold;
    document.getElementById('prop-fill').checked = item.fill;
}

function updateItemProp(prop, val) {
    if (selectedItemIndex === null) return;
    const item = designItems[selectedItemIndex];
    const isText = ['sn', 'ssid', 'pass', 'modelo', 'texto'].includes(item.type);
    
    if (prop === 'width' && isText) item.fontSize = val;
    else if (prop === 'fontSize' && isText) item.fontSize = val;
    else item[prop] = val;
    
    renderDesignItems();
}

function removeSelectedItem() { 
    designItems.splice(selectedItemIndex, 1); 
    selectedItemIndex = null; 
    document.getElementById('propiedades-panel').classList.add('hidden'); 
    renderDesignItems(); 
}

function deselectAll() { 
    selectedItemIndex = null; 
    document.getElementById('propiedades-panel').classList.add('hidden'); 
    renderDesignItems(); 
}

function startDrag(e, index) {
    e.preventDefault(); 
    const item = designItems[index]; 
    const canvas = document.getElementById('label-canvas'); 
    const rect = canvas.getBoundingClientRect();
    const moveHandler = (moveEvent) => {
        const x = moveEvent.clientX - rect.left - 20; 
        const y = moveEvent.clientY - rect.top - 10;
        item.x = Math.max(0, Math.min(x, parseInt(canvas.style.width) - 20));
        item.y = Math.max(0, Math.min(y, parseInt(canvas.style.height) - 10));
        renderDesignItems();
    };
    const upHandler = () => { 
        document.removeEventListener('mousemove', moveHandler); 
        document.removeEventListener('mouseup', upHandler); 
    };
    document.addEventListener('mousemove', moveHandler); 
    document.addEventListener('mouseup', upHandler);
}

async function guardarDiseno() {
    try { 
        const res = await fetch('api.php?controller=EtiquetaTemplates&action=guardar', { 
            method: 'POST', 
            body: JSON.stringify({ id: currentDesignId, config_json: JSON.stringify(designItems) }) 
        });
        if ((await res.json()).status === 'success') { 
            showToast('Diseño Guardado', 'success'); 
            await fetchTemplates(); 
        } 
    } catch (e) { showToast('Error', 'error'); }
}

function mostrarPreviewImpresion() {
    const tpl = window.templates.find(t => t.id == currentDesignId);
    const container = document.getElementById('preview-print-label');
    container.style.width = tpl.ancho + 'mm';
    container.style.height = tpl.alto + 'mm';
    container.style.position = 'relative';
    container.innerHTML = '';

    designItems.forEach(item => {
        const el = document.createElement('div');
        el.style.position = 'absolute';
        el.style.left = (item.x / MM_TO_PX) + 'mm';
        el.style.top = (item.y / MM_TO_PX) + 'mm';
        el.style.color = item.color || '#000';
        
        if (['sn', 'ssid', 'pass', 'modelo', 'texto'].includes(item.type)) {
            el.style.fontSize = (item.fontSize / MM_TO_PX) + 'mm';
            el.style.fontWeight = item.bold ? 'bold' : 'normal';
            el.style.fontFamily = item.fontFamily || 'sans-serif';
            el.textContent = item.sampleText;
        } else {
            el.style.width = (item.width / MM_TO_PX) + 'mm';
            el.style.height = (item.height / MM_TO_PX) + 'mm';
            el.style.border = `${(item.borderWidth || 2) / MM_TO_PX}mm solid currentColor`;
            if (item.fill) el.style.backgroundColor = 'currentColor';
            if (item.type === 'circle') el.style.borderRadius = '50%';
            if (item.type === 'line') { el.style.height = '0'; el.style.borderBottom = el.style.border; el.style.borderTop = 'none'; }
        }
        container.appendChild(el);
    });
    document.getElementById('modal-preview-print').classList.remove('hidden');
}

function cerrarPreviewImpresion() { document.getElementById('modal-preview-print').classList.add('hidden'); }
