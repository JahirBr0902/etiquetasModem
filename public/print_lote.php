<?php
require_once __DIR__ . '/../app/config/db.php';

$lote_id = $_GET['lote_id'] ?? null;

if (!$lote_id) die("ID de lote no proporcionado");

try {
    $database = new Database();
    $conn = $database->getConnection();

    // Obtener información del lote
    $qLote = "SELECT * FROM lotes WHERE id = :id";
    $stLote = $conn->prepare($qLote);
    $stLote->bindParam(':id', $lote_id);
    $stLote->execute();
    $lote = $stLote->fetch(PDO::FETCH_ASSOC);
    if (!$lote) die("Lote no encontrado");

    // Obtener modems con sus modelos y templates
    $query = "SELECT m.*, mod.nombre as modelo_nombre,
              tp.ancho as p_ancho, tp.alto as p_alto, tp.config_json as p_config,
              ts.ancho as s_ancho, ts.alto as s_alto, ts.config_json as s_config
              FROM modems m
              LEFT JOIN modelos_modem mod ON m.modelo_id = mod.id
              LEFT JOIN etiquetas_templates tp ON mod.etiqueta_primaria_id = tp.id
              LEFT JOIN etiquetas_templates ts ON mod.etiqueta_secundaria_id = ts.id
              WHERE m.lote_id = :lote_id
              ORDER BY m.id ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':lote_id', $lote_id);
    $stmt->execute();
    $modems = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) { die("Error: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editor de Impresión - <?php echo $lote['nombre']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 320px;
            --bg-canvas: #cbd5e1;
            --shadow-page: 0 20px 50px rgba(0,0,0,0.2);
        }

        body { margin: 0; padding: 0; background: var(--bg-canvas); font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; display: flex; height: 100vh; }

        .sidebar { 
            width: var(--sidebar-width); background: #0f172a; color: white; height: 100vh; 
            display: flex; flex-direction: column; z-index: 100; box-shadow: 10px 0 30px rgba(0,0,0,0.2);
        }
        .sidebar-header { padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar-content { flex: 1; overflow-y: auto; padding: 20px; }
        .sidebar-footer { padding: 20px; background: rgba(0,0,0,0.2); }

        .btn-action { 
            width: 100%; padding: 12px; border-radius: 12px; border: none; font-weight: 900; cursor: pointer;
            text-transform: uppercase; letter-spacing: 1px; font-size: 10px; margin-bottom: 10px; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn-outline { background: transparent; border: 1px solid rgba(255,255,255,0.2); color: white; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 9px; font-weight: 900; color: rgba(255,255,255,0.4); text-transform: uppercase; margin-bottom: 8px; }
        .form-select { width: 100%; background: #1e293b; border: 1px solid #334155; color: white; padding: 10px; border-radius: 8px; font-size: 12px; outline: none; }

        .inventory-list { display: flex; flex-direction: column; gap: 10px; }
        .inventory-item { 
            background: #1e293b; border: 1px solid #334155; padding: 12px; border-radius: 12px; 
            cursor: grab; transition: all 0.2s;
        }
        .inventory-item:hover { border-color: #3b82f6; background: #2d3a4f; }

        .canvas-area { 
            flex: 1; 
            height: 100vh; 
            overflow-y: auto; 
            padding: 60px 60px 250px 60px; /* Aumentado el margen inferior a 250px */
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 50px; 
            position: relative; 
            scroll-behavior: smooth; 
        }
        
        .page { 
            background: white; 
            box-shadow: var(--shadow-page); 
            position: relative; 
            flex-shrink: 0;
            overflow: hidden;
            border: 1px solid #475569; /* Borde oscuro para ver el final de la hoja */
        }
        .page::after { content: 'PÁGINA ' attr(data-page-num); position: absolute; left: -100px; top: 0; color: #64748b; font-weight: 900; font-size: 14px; width: 80px; text-align: right; }

        .label-placed { 
            position: absolute; cursor: move; border: 0.1mm solid #ddd; 
            background: white; display: flex; align-items: center; justify-content: center;
        }
        .label-placed:hover { outline: 2px solid #3b82f6; border-color: transparent; }
        .label-placed.selected { outline: 2px solid #3b82f6; border-color: transparent; z-index: 50; }
        
        /* Recuadro de selección */
        .selection-box {
            position: fixed; border: 1px solid #3b82f6; background: rgba(59, 130, 246, 0.1);
            pointer-events: none; z-index: 1000; display: none;
        }
        
        .label-controls {
            position: absolute; top: -35px; left: 0; background: #3b82f6; border-radius: 8px;
            display: none; gap: 5px; padding: 4px; z-index: 100;
        }
        .label-placed.selected .label-controls { display: flex; }
        .ctrl-btn { 
            background: white; border: none; width: 24px; height: 24px; border-radius: 4px; 
            color: #3b82f6; font-size: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .ctrl-btn.danger { color: #ef4444; }

        .label-content { position: relative; overflow: hidden; pointer-events: none; flex-shrink: 0; }
        .label-item { position: absolute; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; pointer-events: none; }
        .label-item img { width: 100%; height: 100%; object-fit: contain; }

        @media print {
            @page { margin: 0; }
            body { background: white !important; height: auto !important; display: block !important; margin: 0 !important; padding: 0 !important; }
            .sidebar, .no-print { display: none !important; }
            .canvas-area { padding: 0 !important; margin: 0 !important; height: auto !important; display: block !important; background: white !important; overflow: visible !important; }
            .page { 
                margin: 0 !important; 
                box-shadow: none !important; 
                border: none !important; 
                page-break-after: always !important; 
                flex-shrink: unset !important;
                display: block !important;
                position: relative !important;
            }
            .page:last-child { page-break-after: avoid !important; }
            .page::after { display: none !important; }
            .label-placed { border: 0.1mm solid #000 !important; }
            .label-controls { display: none !important; }
        }
    </style>
</head>
<body>

    <aside class="sidebar no-print">
        <div class="sidebar-header">
            <div style="font-size: 9px; font-weight: 900; color: #3b82f6; letter-spacing: 2px; margin-bottom: 5px;">DEPARTAMENTO IMPRESIÓN</div>
            <h1 style="margin: 0; font-size: 18px; font-weight: 900; letter-spacing: -1px; text-transform: uppercase;">Acomodo Manual</h1>
        </div>

        <div class="sidebar-content">
            <div class="form-group">
                <label>Formato de Papel</label>
                <select id="paper-size" class="form-select" onchange="updatePaperSize()">
                    <option value="letter">Carta (Letter)</option>
                    <option value="a4">A4</option>
                    <option value="legal">Oficio (Legal)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Herramientas</label>
                <button class="btn-action btn-outline" onclick="autoArrange()">
                    <i class="fas fa-magic"></i> Auto-Acomodo Inteligente
                </button>
                <button class="btn-action btn-outline" onclick="addNewPage()">
                    <i class="fas fa-plus"></i> Añadir Nueva Hoja
                </button>
                <button class="btn-action btn-outline" onclick="clearPages()">
                    <i class="fas fa-eraser"></i> Limpiar Lienzo
                </button>
            </div>

            <div class="form-group">
                <label>Módems Pendientes (<span id="unplaced-count">0</span>)</label>
                <div id="inventory" class="inventory-list"></div>
            </div>
        </div>

        <div class="sidebar-footer">
            <button class="btn-action btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Generar Impresión
            </button>
            <button class="btn-action btn-outline" onclick="window.close()">Salir del Editor</button>
        </div>
    </aside>

    <main id="canvas-area" class="canvas-area" onmousedown="startMarquee(event)">
        <!-- Hojas generadas aquí -->
    </main>
    <div id="selection-box" class="selection-box"></div>

    <script>
        const modems = <?php echo json_encode($modems); ?>;
        const paperSizes = {
            letter: { w: 215.9, h: 279.4 },
            a4: { w: 210, h: 297 },
            legal: { w: 215.9, h: 355.6 }
        };

        let state = {
            currentPaperSize: 'letter',
            pages: [[]], 
            unplaced: [...modems],
            selectedIds: [], // Ahora es un array
            isDragging: false,
            isMarquee: false,
            startX: 0,
            startY: 0,
            marqueeStartX: 0,
            marqueeStartY: 0
        };

        function init() {
            renderInventory();
            renderPages();
        }

        function renderInventory() {
            const container = document.getElementById('inventory');
            document.getElementById('unplaced-count').textContent = state.unplaced.length;
            container.innerHTML = state.unplaced.map((m, idx) => `
                <div class="inventory-item" draggable="true" ondragstart="dragLabel(event, ${idx})">
                    <div style="font-family:'JetBrains Mono'; font-size:11px; font-weight:bold; color:#3b82f6;">${m.sn}</div>
                    <div style="font-size:9px; font-weight:900; color:#94a3b8; text-transform:uppercase; margin-top:4px;">${m.modelo_nombre}</div>
                </div>
            `).join('');
        }

        function renderPages() {
    const area = document.getElementById('canvas-area');
    area.innerHTML = '';
    
    state.pages.forEach((pageLabels, pageIdx) => {
        const pageEl = document.createElement('div');
        pageEl.className = 'page';
        pageEl.dataset.pageNum = pageIdx + 1;
        pageEl.style.width = paperSizes[state.currentPaperSize].w + 'mm';
        pageEl.style.height = paperSizes[state.currentPaperSize].h + 'mm';
        
        pageEl.ondragover = (e) => e.preventDefault();
        pageEl.ondrop = (e) => dropLabel(e, pageIdx);

        pageLabels.forEach((label) => {
            const isSelected = state.selectedIds.includes(label.uniqueId);
            const lEl = document.createElement('div');
            lEl.className = `label-placed ${isSelected ? 'selected' : ''}`;
            lEl.dataset.id = label.uniqueId;
            
            const isRotated = (label.rotation % 180 !== 0);
            lEl.style.width = (isRotated ? label.h : label.w) + 'mm';
            lEl.style.height = (isRotated ? label.w : label.h) + 'mm';
            lEl.style.left = label.x + 'mm';
            lEl.style.top = label.y + 'mm';
            
            lEl.onmousedown = (e) => selectLabel(e, label.uniqueId);
            
            lEl.innerHTML = `
                <div class="label-controls no-print">
                    <button class="ctrl-btn" onmousedown="rotateLabel(event, '${label.uniqueId}', 90)"><i class="fas fa-sync"></i></button>
                    <button class="ctrl-btn danger" onmousedown="removeLabel(event, '${label.uniqueId}')"><i class="fas fa-trash"></i></button>
                </div>
                <div class="label-content" style="transform: rotate(${label.rotation}deg); width:${label.w}mm; height:${label.h}mm;">
                    ${renderLabelContent(label)}
                </div>
            `;
            pageEl.appendChild(lEl);
        });

        area.appendChild(pageEl);

        const separator = document.createElement('div');
        separator.className = 'no-print page-separator';
        separator.innerHTML = `<span>— Fin de hoja ${pageIdx + 1} —</span>`;
        area.appendChild(separator);
    });

    // Generar códigos de barras después del renderizado
    requestAnimationFrame(() => {
        document.querySelectorAll('svg[data-barcode-value]').forEach(svg => {
            try {
                JsBarcode(svg, svg.dataset.barcodeValue, {
                    format: 'CODE128',
                    displayValue: true,
                    fontSize: 24,
                    margin: 2,
                    width: 2,
                    height: 50,
                    flat: true,
                    background: "#ffffff"
                });
            } catch (err) {
                console.warn('Barcode error en', svg.id, err);
            }
        });
    });
}

        function renderLabelContent(label) {
            const config = label.isSecondary ? label.data.s_config : label.data.p_config;
            if (!config) return '';
            const items = JSON.parse(config);
            const ancho = label.isSecondary ? label.data.s_ancho : label.data.p_ancho;
            const alto = label.isSecondary ? label.data.s_alto : label.data.p_alto;

            return items.map(item => {
                const itemColor = item.color ?? '#000000';
                let style = `left:${(item.x/5.0 * 100 / ancho)}%; `;
                style += `top:${(item.y/5.0 * 100 / alto)}%; `;
                style += `width:${(item.width/5.0 * 100 / ancho)}%; `;
                style += `height:${(item.height/5.0 * 100 / alto)}%; `;
                style += `color:${itemColor}; z-index:${item.zIndex || 0}; `;

                if (['sn', 'ssid', 'pass', 'ssid2', 'ssid3', 'modelo', 'texto'].includes(item.type)) {
                    style += `font-size:${(item.fontSize / 4.5)}mm; `;
                    style += `font-weight:${item.bold ? 'bold' : 'normal'}; `;
                    style += `font-family:${item.fontFamily || 'sans-serif'}; `;
                    
                    let content = item.sampleText;
                    if (item.type === 'sn') content = label.data.sn;
                    if (item.type === 'ssid') content = label.data.ssid;
                    if (item.type === 'ssid2') content = label.data.ssid + "_2.4";
                    if (item.type === 'ssid3') content = label.data.ssid + "_5G";
                    if (item.type === 'pass') content = label.data.password;
                    if (item.type === 'modelo') content = label.data.modelo_nombre;
                    return `<div class="label-item" style="${style}">${content}</div>`;
                } else if (['barcode', 'barcode_pass', 'barcode_model'].includes(item.type)) {
                    let val = label.data.sn;
                    if (item.type === 'barcode_pass') val = label.data.password;
                    if (item.type === 'barcode_model') val = label.data.modelo_nombre;
                    
                    const suffix = item.type.split('_')[1] || 'sn';
                    const bcId = `bc_${label.data.sn.replace(/[^a-zA-Z0-9]/g, '')}_${suffix}_${label.isSecondary ? 's' : 'p'}`;
                    
                    // Añadimos shape-rendering: crispEdges para máxima nitidez
                    return `<div class="label-item" style="${style}">
                        <svg id="${bcId}" data-barcode-value="${val}" style="width:100%; height:100%; shape-rendering: crispEdges;"></svg>
                    </div>`;
                } else if (item.type === 'qr_pass' || item.type === 'qr_wifi') {
                    let text = label.data.password;
                    if (item.type === 'qr_wifi') {
                        let ssid = label.data.ssid;
                        if (item.wifiBand === '2.4g') ssid += "_2.4";
                        if (item.wifiBand === '5g') ssid += "_5G";
                        text = `WIFI:S:${ssid};T:WPA;P:${label.data.password};;`;
                    }
                    const qrUrl = `https://bwipjs-api.metafloor.com/?bcid=qrcode&text=${encodeURIComponent(text)}&scale=2`;
                    return `<div class="label-item" style="${style} background:white; padding:1mm;">
                        <img src="${qrUrl}" style="width:100%; height:100%; object-fit:contain;">
                    </div>`;
                } else if (item.type === 'image') {
                    return `<div class="label-item" style="${style}"><img src="${item.src}"></div>`;
                } else if (item.type === 'rect' || item.type === 'circle') {
                    style += `border: 0.1mm solid ${itemColor}; `;
                    if (item.fill) style += `background-color: ${itemColor} !important; `;
                    if (item.type === 'circle') style += `border-radius: 50%; `;
                    return `<div class="label-item" style="${style}"></div>`;
                }
                return '';
            }).join('');
        }

        // --- ACCIONES ---

        function updatePaperSize() {
            state.currentPaperSize = document.getElementById('paper-size').value;
            renderPages();
        }

        function addNewPage() {
            state.pages.push([]);
            renderPages();
        }

        function clearPages() {
            state.unplaced = [...modems];
            state.pages = [[]];
            state.selectedIds = [];
            renderPages();
            renderInventory();
        }

        function dragLabel(e, idx) {
            e.dataTransfer.setData('text/plain', idx);
        }

        function dropLabel(e, pageIdx) {
            e.preventDefault();
            const modemIdx = e.dataTransfer.getData('text/plain');
            if (modemIdx === "") return;

            const modem = state.unplaced[modemIdx];
            const rect = e.currentTarget.getBoundingClientRect();
            const mmScale = paperSizes[state.currentPaperSize].w / rect.width;
            const x = (e.clientX - rect.left) * mmScale;
            const y = (e.clientY - rect.top) * mmScale;

            state.pages[pageIdx].push({
                uniqueId: Math.random().toString(36).substr(2, 9),
                data: modem, x, y, w: modem.p_ancho, h: modem.p_alto, rotation: 0, isSecondary: false
            });
            
            if (modem.s_config) {
                state.pages[pageIdx].push({
                    uniqueId: Math.random().toString(36).substr(2, 9),
                    data: modem, x: x + 2, y: y + 2, w: modem.s_ancho, h: modem.s_alto, rotation: 0, isSecondary: true
                });
            }

            state.unplaced.splice(modemIdx, 1);
            renderInventory();
            renderPages();
        }

        function selectLabel(e, id) {
            e.stopPropagation();
            if (e.ctrlKey || e.metaKey) {
                if (state.selectedIds.includes(id)) {
                    state.selectedIds = state.selectedIds.filter(i => i !== id);
                } else {
                    state.selectedIds.push(id);
                }
            } else {
                if (!state.selectedIds.includes(id)) {
                    state.selectedIds = [id];
                }
            }
            state.isDragging = true;
            state.startX = e.clientX;
            state.startY = e.clientY;

            // Calcular offsets iniciales respecto al ratón en mm
            state.dragOffsets = [];
            state.selectedIds.forEach(sid => {
                state.pages.forEach((p, pIdx) => p.forEach(l => {
                    if (l.uniqueId === sid) {
                        const pageEl = document.querySelector(`.page[data-page-num="${pIdx + 1}"]`);
                        const rect = pageEl.getBoundingClientRect();
                        const mmScale = paperSizes[state.currentPaperSize].w / rect.width;
                        
                        state.dragOffsets.push({
                            id: sid,
                            dx: l.x - (e.clientX - rect.left) * mmScale,
                            dy: l.y - (e.clientY - rect.top) * mmScale
                        });
                    }
                }));
            });

            renderPages();
        }

        function startMarquee(e) {
            if (e.button !== 0) return;
            state.isMarquee = true;
            state.marqueeStartX = e.clientX;
            state.marqueeStartY = e.clientY;
            
            // Deseleccionar todo inmediatamente al hacer click en el fondo
            state.selectedIds = [];
            state.dragOffsets = [];
            
            const box = document.getElementById('selection-box');
            box.style.display = 'block';
            box.style.left = e.clientX + 'px';
            box.style.top = e.clientY + 'px';
            box.style.width = '0px';
            box.style.height = '0px';

            renderPages();
        }

        function deselectLabel() {
            state.selectedIds = [];
            state.isDragging = false;
            state.dragOffsets = [];
            renderPages();
        }

        function rotateLabel(e, id, deg) {
            e.stopPropagation();
            const idsToRotate = state.selectedIds.includes(id) ? state.selectedIds : [id];
            state.pages.forEach(p => p.forEach(l => {
                if (idsToRotate.includes(l.uniqueId)) {
                    l.rotation = (l.rotation + deg) % 360;
                }
            }));
            renderPages();
        }

        function removeLabel(e, id) {
            e.stopPropagation();
            const idsToRemove = state.selectedIds.includes(id) ? state.selectedIds : [id];
            
            idsToRemove.forEach(idToRemove => {
                let removedModem = null;
                state.pages.forEach((page, pIdx) => {
                    const lIdx = page.findIndex(l => l.uniqueId === idToRemove);
                    if (lIdx > -1) {
                        removedModem = page[lIdx].data;
                        state.pages[pIdx].splice(lIdx, 1);
                    }
                });
                
                if (removedModem) {
                    const stillOnPages = state.pages.some(p => p.some(l => l.data.id === removedModem.id));
                    if (!stillOnPages && !state.unplaced.find(m => m.id === removedModem.id)) {
                        state.unplaced.push(removedModem);
                    }
                }
            });
            
            state.selectedIds = state.selectedIds.filter(id => !idsToRemove.includes(id));
            renderInventory();
            renderPages();
        }

        // ... rotateLabel and removeLabel remain same ...

        window.onmousemove = (e) => {
            if (state.isMarquee) {
                const box = document.getElementById('selection-box');
                const x = Math.min(e.clientX, state.marqueeStartX);
                const y = Math.min(e.clientY, state.marqueeStartY);
                const w = Math.abs(e.clientX - state.marqueeStartX);
                const h = Math.abs(e.clientY - state.marqueeStartY);
                
                box.style.left = x + 'px';
                box.style.top = y + 'px';
                box.style.width = w + 'px';
                box.style.height = h + 'px';
                
                const boxRect = box.getBoundingClientRect();
                const newSelection = [];
                document.querySelectorAll('.label-placed').forEach(el => {
                    const elRect = el.getBoundingClientRect();
                    if (!(elRect.left > boxRect.right || elRect.right < boxRect.left || 
                          elRect.top > boxRect.bottom || elRect.bottom < boxRect.top)) {
                        newSelection.push(el.dataset.id);
                    }
                });
                state.selectedIds = newSelection;
                renderPages();
                return;
            }

            if (!state.isDragging || state.selectedIds.length === 0) return;
            
            // Detectar página bajo el mouse
            const elementsUnderMouse = document.elementsFromPoint(e.clientX, e.clientY);
            const targetPageEl = elementsUnderMouse.find(el => el.classList.contains('page'));
            
            if (targetPageEl) {
                const targetPageIndex = parseInt(targetPageEl.dataset.pageNum) - 1;
                const rect = targetPageEl.getBoundingClientRect();
                const mmScale = paperSizes[state.currentPaperSize].w / rect.width;
                
                const mouseXmm = (e.clientX - rect.left) * mmScale;
                const mouseYmm = (e.clientY - rect.top) * mmScale;

                state.selectedIds.forEach(id => {
                    const offset = state.dragOffsets.find(o => o.id === id);
                    if (!offset) return;

                    let sourcePageIndex = -1;
                    let targetLabel = null;
                    
                    state.pages.forEach((p, pIdx) => p.forEach(l => {
                        if (l.uniqueId === id) {
                            targetLabel = l;
                            sourcePageIndex = pIdx;
                        }
                    }));

                    if (targetLabel) {
                        // Actualizar posición relativa a la página actual del mouse
                        targetLabel.x = mouseXmm + offset.dx;
                        targetLabel.y = mouseYmm + offset.dy;

                        // Si cambió de página, mover al array correspondiente
                        if (sourcePageIndex !== targetPageIndex) {
                            const lIdx = state.pages[sourcePageIndex].findIndex(l => l.uniqueId === id);
                            state.pages[sourcePageIndex].splice(lIdx, 1);
                            state.pages[targetPageIndex].push(targetLabel);
                        }
                    }
                });
            }

            state.startX = e.clientX;
            state.startY = e.clientY;
            renderPages();
        };

        window.onmouseup = () => { 
            state.isDragging = false; 
            if (state.isMarquee) {
                state.isMarquee = false;
                document.getElementById('selection-box').style.display = 'none';
            }
        };

    function autoArrange() {
    const paper = paperSizes[state.currentPaperSize];
    const margin = 5;
    const gap = 2;

    const allModemsMap = new Map();
    state.unplaced.forEach(m => allModemsMap.set(m.id, m));
    state.pages.forEach(p => p.forEach(l => allModemsMap.set(l.data.id, l.data)));
    const allModems = [...allModemsMap.values()];

    const itemsToPlace = [];
    allModems.forEach(m => {
        itemsToPlace.push({ data: m, w: parseFloat(m.p_ancho), h: parseFloat(m.p_alto), isSec: false });
        if (m.s_config) {
            itemsToPlace.push({ data: m, w: parseFloat(m.s_ancho), h: parseFloat(m.s_alto), isSec: true });
        }
    });

    state.pages = [[]];
    state.unplaced = [];

    let curPage = 0;
    let curX = margin;
    let curY = margin;
    let maxRowH = 0;
    const usableW = paper.w - margin * 2;
    const usableH = paper.h - margin * 2;

    itemsToPlace.forEach(item => {
        let w = item.w;
        let h = item.h;
        let rotation = 0;

        // ¿No cabe normal en la fila? Intentar rotada
        if (curX + w > margin + usableW && curX > margin) {
            if (curX + h <= margin + usableW && h <= usableH) {
                // Rotada sí cabe en esta fila, usarla
                [w, h] = [h, w];
                rotation = 90;
            } else {
                // Ninguna orientación cabe en esta fila, saltar a nueva fila
                curX = margin;
                curY += maxRowH + gap;
                maxRowH = 0;
                // En la nueva fila, ¿conviene rotarla para ahorrar alto de fila?
                if (h > w && w <= usableH) {
                    [w, h] = [h, w];
                    rotation = 90;
                }
            }
        }

        // ¿No cabe verticalmente en esta página?
        if (curY + h > margin + usableH) {
            curPage++;
            state.pages.push([]);
            curX = margin;
            curY = margin;
            maxRowH = 0;
            rotation = 0;
            w = item.w;
            h = item.h;
        }

        state.pages[curPage].push({
            uniqueId: Math.random().toString(36).substr(2, 9),
            data: item.data,
            x: curX,
            y: curY,
            w: item.w,   // dimensión original siempre
            h: item.h,
            rotation,
            isSecondary: item.isSec
        });

        curX += w + gap;
        if (h > maxRowH) maxRowH = h;
    });

    renderInventory();
    renderPages();
}

        init();
    </script>
</body>
</html>