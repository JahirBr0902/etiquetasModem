<!DOCTYPE html>
<html lang="es">
<?php include 'views/layout/head.php'; ?>
<body class="bg-slate-50 min-h-screen flex">

    <!-- MENÚ LATERAL -->
    <?php include 'views/layout/sidebar.php'; ?>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        
        <!-- BARRA SUPERIOR -->
        <?php include 'views/layout/header.php'; ?>

        <!-- VISTAS / SECCIONES -->
        <div class="p-8">
            <?php 
                include 'views/components/dashboard.php';
                include 'views/components/produccion.php';
                include 'views/components/impresion.php';
                include 'views/components/modelos.php';
                include 'views/components/templates.php';
            ?>
        </div>
    </main>

    <!-- COMPONENTES ESPECIALES (DISEÑADOR Y MODALES) -->
    <?php 
        include 'views/components/designer.php';
        include 'views/components/modals.php';
    ?>

    <!-- NOTIFICACIONES -->
    <div id="toast-container" class="fixed bottom-10 right-10 z-[300] flex flex-col gap-3"></div>

    <!-- SCRIPTS -->
    <script src="assets/js/app.js"></script>
    <script src="assets/js/designer.js"></script>
</body>
</html>