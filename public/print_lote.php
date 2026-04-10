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

function replacePlaceholders($text, $modem) {
    $ssid2 = $modem['ssid'] . "_2.4";
    $ssid3 = $modem['ssid'] . "_5G";
    $search = ['{sn}', '{ssid}', '{ssid2}', '{ssid3}', '{pass}', '{modelo}'];
    $replace = [$modem['sn'], $modem['ssid'], $ssid2, $ssid3, $modem['password'], $modem['modelo_nombre']];
    return str_replace($search, $replace, $text);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Imprimir Lote - <?php echo $lote['nombre']; ?></title>
    <!-- FUENTES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; padding: 0; background: #f8fafc; font-family: sans-serif; }
        .print-grid { 
            display: flex; flex-wrap: wrap; gap: 2mm; padding: 5mm; 
            justify-content: flex-start; max-width: 210mm; margin: 0 auto;
        }
        .label-box { 
            background: white; position: relative; overflow: hidden; 
            border: 0.1mm solid #e2e8f0; -webkit-print-color-adjust: exact; print-color-adjust: exact;
            break-inside: avoid; page-break-inside: avoid;
        }
        .item { position: absolute; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; }
        .item img { width: 100%; height: 100%; object-fit: contain; }
        
        .no-print-bar { 
            position: sticky; top: 0; background: #4f46e5; color: white; 
            padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; 
            z-index: 1000; font-size: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .btn-print { background: white; color: #4f46e5; padding: 8px 20px; border-radius: 12px; text-decoration: none; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; }

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
            <span style="font-weight:900; letter-spacing:1px;">LOTE: <?php echo strtoupper($lote['nombre']); ?></span>
            <span style="opacity:0.7; font-weight:bold;"><?php echo count($modems); ?> EQUIPOS LISTOS</span>
        </div>
        <a href="javascript:window.print()" class="btn-print">INICIAR IMPRESIÓN</a>
    </div>

    <div class="print-grid">
        <?php 
        $renderLabel = function($config, $ancho, $alto, $modem) {
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
                        
                        $content = ($item['type'] === 'texto') ? $item['sampleText'] : $item['type'];
                        if ($item['type'] === 'sn') $content = $modem['sn'];
                        if ($item['type'] === 'ssid') $content = $modem['ssid'];
                        if ($item['type'] === 'ssid2') $content = $modem['ssid'] . "_2.4";
                        if ($item['type'] === 'ssid3') $content = $modem['ssid'] . "_5G";
                        if ($item['type'] === 'pass') $content = $modem['password'];
                        if ($item['type'] === 'modelo') $content = $modem['modelo_nombre'];
                        
                        echo '<div class="item" style="' . $style . '">' . replacePlaceholders($content, $modem) . '</div>';
                    } elseif ($item['type'] === 'barcode') {
                        // Implementación simple de código de barras (simulado con líneas o una imagen si tuviéramos generador)
                        echo '<div class="item" style="' . $style . '; flex-direction:column; justify-content:flex-end; padding-bottom:1mm;">';
                        echo '<div style="width:90%; height:70%; background: repeating-linear-gradient(90deg, #000, #000 1px, #fff 1px, #fff 2px) !important;"></div>';
                        echo '<span style="font-size:1.5mm; font-weight:bold; margin-top:0.5mm;">'.$modem['sn'].'</span>';
                        echo '</div>';
                    } elseif ($item['type'] === 'image') {
                        echo '<div class="item" style="' . $style . '"><img src="' . ($item['src'] ?? '') . '"></div>';
                    } elseif ($item['type'] === 'rect' || $item['type'] === 'circle') {
                        $style .= "border: 0.1mm solid ".$itemColor."; ";
                        if (isset($item['fill']) && $item['fill']) $style .= "background-color: ".$itemColor." !important; ";
                        if ($item['type'] === 'circle') $style .= "border-radius: 50%; ";
                        echo '<div class="item" style="' . $style . '"></div>';
                    } elseif ($item['type'] === 'line') {
                        $style .= "border-top: 0.1mm solid ".$itemColor."; ";
                        echo '<div class="item" style="' . $style . '"></div>';
                    }
                endforeach; ?>
            </div>
        <?php };

        foreach ($modems as $modem) {
            $renderLabel($modem['p_config'], $modem['p_ancho'], $modem['p_alto'], $modem);
            if ($modem['s_config']) {
                $renderLabel($modem['s_config'], $modem['s_ancho'], $modem['s_alto'], $modem);
            }
        }
        ?>
    </div>

</body>
</html>