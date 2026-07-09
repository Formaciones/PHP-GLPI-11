<?php

// Carga el entorno completo de GLPI: sesion, clases, DB, permisos, etc.
include('../../../inc/includes.php');

// Usa el mismo derecho que Formaciones. Si no hay permiso de lectura,
// GLPI detiene la ejecucion y muestra su mensaje estandar de permisos.
Session::checkRight(PluginExportacionesExportacion::$rightname, READ);

// La exportacion se lanza con GET porque solo descarga datos y no modifica la BD.
if (isset($_GET['export'])) {
    try {
        // La clase envia el CSV al navegador y termina la ejecucion con exit.
        PluginExportacionesExportacion::exportToCsv();
    } catch (Throwable $e) {
        // Si falta la tabla u ocurre algun error, se muestra al volver a la pagina.
        Session::addMessageAfterRedirect($e->getMessage(), false, ERROR);
        Html::redirect(PluginExportacionesExportacion::getPageUrl(false));
    }
}

// URL que ejecuta la exportacion. Se construye desde la clase para no depender
// de $_SERVER['PHP_SELF'], que puede cambiar segun la configuracion de GLPI.
$export_url = PluginExportacionesExportacion::getPageUrl(false) . '?export=1';

// Cabecera estandar de GLPI. La entrada esta en el menu Administracion.
Html::header(
    PluginExportacionesExportacion::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'admin',
    PluginExportacionesExportacion::class
);

// Contenido sencillo de la pagina: un unico boton para iniciar la descarga.
echo "<div class='center'>";
echo "<br />";
echo "<h1>Exportar datos de formaciones a CSV</h1><hr />";
echo "<p>";
echo "<a class='btn btn-primary' href='" . htmlescape($export_url) . "'>";
echo htmlescape(__('Exportar datos', 'exportaciones'));
echo "</a>";
echo "</p>";
echo "</div>";

// Pie estandar de GLPI.
Html::footer();
