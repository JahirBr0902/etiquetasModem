<?php
require_once __DIR__ . '/../app/config/db.php';

$id = $_GET['id'] ?? null;
$qty = $_GET['qty'] ?? 20; 

if (!$id) die("ID de modelo no proporcionado");

try {
    $database = new Database();
    $conn = $database->getConnection();

    $query = "SELECT m.*, 
              tp.ancho as p_ancho, tp.alto as p_alto, tp.config_json as p_config,
              ts.ancho as s_ancho, ts.alto as s_alto, ts.config_json as s_config
              FROM modelos_modem m
              LEFT JOIN etiquetas_templates tp ON m.etiqueta_primaria_id = tp.id
              LEFT JOIN etiquetas_templates ts ON m.etiqueta_secundaria_id = ts.id
              WHERE m.id = :id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $modelo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$modelo) die("Modelo no encontrado");

} catch (Exception $e) { die("Error: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba - <?php echo $modelo['nombre']; ?></title>
    <!-- FUENTES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; background: #f8fafc; font-family: sans-serif; }
        
        .print-grid { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 1.5mm; 
            padding: 5mm; 
            justify-content: flex-start;
            max-width: 210mm;
            margin: 0 auto;
        }

        .label-box { 
            background: white; 
            position: relative; 
            overflow: hidden; 
            border: 0.1mm solid #e2e8f0;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact;
            /* EVITAR CORTES ENTRE PÁGINAS */
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .item { position: absolute; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; }
        .item img { width: 100%; height: 100%; object-fit: contain; }
        
        /* Barra superior */
        .no-print-bar { 
            position: sticky; top: 0; 
            background: #0f172a; color: white; 
            padding: 10px 20px; 
            display: flex; justify-content: space-between; align-items: center; 
            z-index: 1000; font-size: 11px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-print { background: #3b82f6; color: white; padding: 6px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; text-transform: uppercase; }
        .qty-input { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 4px; border-radius: 4px; width: 45px; text-align: center; }

        @media print {
            @page { margin: 5mm; }
            body { background: white; margin: 0; padding: 0; }
            .no-print-bar { display: none !important; }
            .print-grid { padding: 0; gap: 1mm; width: 100%; margin: 0; }
            .label-box { border: 0.1mm solid #000; box-shadow: none; margin-bottom: 1mm; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="display:flex; align-items:center; gap:20px;">
            <span style="font-weight:bold; opacity:0.8;">PRUEBA: <?php echo strtoupper($modelo['nombre']); ?></span>
            <form method="GET" style="display:flex; align-items:center; gap:8px;">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <span>Cant. Juegos:</span>
                <input type="number" name="qty" value="<?php echo $qty; ?>" class="qty-input">
                <button type="submit" style="background:white; border:none; color:#0f172a; padding:4px 10px; border-radius:4px; cursor:pointer; font-weight:bold; font-size:10px;">ACTUALIZAR</button>
            </form>
        </div>
        <a href="javascript:window.print()" class="btn-print">IMPRIMIR PDF</a>
    </div>

    <div class="print-grid">
        <?php 
        $renderLabel = function($config, $ancho, $alto, $modeloNombre) {
            if (!$config) return;
            $items = json_decode($config, true);
            ?>
            <div class="label-box" style="width: <?php echo $ancho; ?>mm; height: <?php echo $alto; ?>mm;">
                <?php foreach ($items as $item): 
                    $itemColor = $item['color'] ?? '#000000';
                    $itemZIndex = $item['zIndex'] ?? 0;
                    $style = "left:".($item['x']/5.0 * 100 / $ancho)."%; ";
                    $style .= "top:".($item['y']/5.0 * 100 / $alto)."%; ";
                    $style .= "width:".($item['width']/5.0 * 100 / $ancho)."%; ";
                    $style .= "height:".($item['height']/5.0 * 100 / $alto)."%; ";
                    $style .= "color:".$itemColor."; ";
                    $style .= "z-index:".$itemZIndex."; ";
                    
                    if (in_array($item['type'], ['sn', 'ssid', 'pass', 'ssid2', 'ssid3', 'modelo', 'texto'])) {
                        $style .= "font-size:".($item['fontSize'] / 4.5)."mm; ";
                        $style .= "font-weight:".((isset($item['bold']) && $item['bold']) ? 'bold' : 'normal')."; ";
                        $style .= "font-family:".($item['fontFamily'] ?? 'sans-serif')."; ";
                        
                        $content = $item['sampleText'] ?? '';
                        if ($item['type'] === 'sn') $content = "SN-12345678";
                        if ($item['type'] === 'ssid') $content = "WITMAC_WIFI";
                        if ($item['type'] === 'pass') $content = "PASS1234";
                        if ($item['type'] === 'modelo') $content = $modeloNombre;
                        
                        echo '<div class="item" style="' . $style . '">' . $content . '</div>';
                    } elseif (in_array($item['type'], ['barcode', 'barcode_pass', 'barcode_model'])) {
                        $text = "SN-12345678";
                        if ($item['type'] === 'barcode_pass') $text = "PASS1234";
                        if ($item['type'] === 'barcode_model') $text = $modeloNombre;
                        $barcodeUrl = "https://bwipjs-api.metafloor.com/?bcid=code128&text=" . urlencode($text) . "&scale=2&rotate=N&includetext=false";
                        
                        echo '<div class="item" style="' . $style . '; flex-direction:column; justify-content:center; align-items:center; background:white;">';
                        echo '<img src="'.$barcodeUrl.'" style="width:95%; height:80%; object-fit:stretch;">';
                        echo '<span style="font-size:1.8mm; font-weight:900; margin-top:0.2mm; color:black; font-family:\'JetBrains Mono\', monospace;">' . $text . '</span>';
                        echo '</div>';
                    } elseif ($item['type'] === 'qr_pass' || $item['type'] === 'qr_wifi') {
                        $text = "PASS1234";
                        if ($item['type'] === 'qr_wifi') {
                            $text = "WIFI:S:WITMAC_WIFI;T:WPA;P:PASS1234;;";
                        }
                        $qrUrl = "https://bwipjs-api.metafloor.com/?bcid=qrcode&text=" . urlencode($text) . "&scale=2";
                        echo '<div class="item" style="' . $style . '; background:white; padding:1mm;">';
                        echo '<img src="'.$qrUrl.'" style="width:100%; height:100%; object-fit:contain;">';
                        echo '</div>';
                    } elseif ($item['type'] === 'image') {
                        echo '<div class="item" style="' . $style . '"><img src="' . ($item['src'] ?? '') . '"></div>';
                    } elseif ($item['type'] === 'rect' || $item['type'] === 'circle') {
                        $style .= "border: 0.2mm solid ".$itemColor."; ";
                        if (isset($item['fill']) && $item['fill']) $style .= "background-color: ".$itemColor." !important; ";
                        if ($item['type'] === 'circle') $style .= "border-radius: 50%; ";
                        echo '<div class="item" style="' . $style . '"></div>';
                    } elseif ($item['type'] === 'line') {
                        $style .= "border-top: 0.2mm solid ".$itemColor."; ";
                        echo '<div class="item" style="' . $style . '"></div>';
                    }
                endforeach; ?>
            </div>
        <?php };

        for ($i = 0; $i < $qty; $i++) {
            $renderLabel($modelo['p_config'], $modelo['p_ancho'], $modelo['p_alto'], $modelo['nombre']);
            if ($modelo['etiqueta_secundaria_id']) {
                $renderLabel($modelo['s_config'], $modelo['s_ancho'], $modelo['s_alto'], $modelo['nombre']);
            }
        }
        ?>
    </div>

</body>
</html>