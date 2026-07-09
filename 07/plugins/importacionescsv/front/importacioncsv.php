<?php

// Carga el entorno completo de GLPI: sesion, clases, DB, permisos, etc.
include('../../../inc/includes.php');

// Usa el mismo derecho que Formaciones. Si no hay permiso de lectura,
// GLPI detiene la ejecucion y muestra su mensaje estandar de permisos.
Session::checkRight(PluginImportacionescsvImportacioncsv::$rightname, READ);

// El CSV se recibe mediante POST porque el navegador debe enviar el archivo.
if (isset($_POST['import'])) {
    try {
        // Llama a la clase del plugin, que valida y procesa el fichero subido.
        $inserted = PluginImportacionescsvImportacioncsv::importFromUploadedCsv($_FILES['csv_file'] ?? []);

        // El mensaje se mostrara despues de redirigir al listado.
        Session::addMessageAfterRedirect(sprintf(
            __('Importacion CSV completada. Registros insertados: %d', 'importacionescsv'),
            $inserted
        ));
    } catch (Throwable $e) {
        // Si falla el fichero, el CSV o la base de datos, se muestra el error al usuario.
        Session::addMessageAfterRedirect($e->getMessage(), false, ERROR);
    }

    // Tras importar, se envia al usuario al listado del plugin Formaciones.
    Html::redirect(PluginFormacionesFormacion::getSearchURL(false));
}

// Cabecera estandar de GLPI. La entrada esta en el menu Administracion.
Html::header(
    PluginImportacionescsvImportacioncsv::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'admin',
    PluginImportacionescsvImportacioncsv::class
);

// Formulario de subida. multipart/form-data es obligatorio para enviar archivos.
echo "<div class='center'>";
echo "<br />";
echo "<h1>Importar datos de formaciones desde CSV</h1><hr />";
echo "<form method='post' action='" . htmlescape(PluginImportacionescsvImportacioncsv::getPageUrl(false)) . "' enctype='multipart/form-data'>";
echo "<input type='hidden' name='_glpi_csrf_token' value='" . htmlescape(Session::getNewCSRFToken()) . "'>";
echo "<p>";
echo "<input type='file' name='csv_file' accept='.csv,text/csv' required>";
echo "</p>";
echo "<p>";
echo "<button type='submit' name='import' value='1' class='btn btn-primary'>";
echo htmlescape(__('Importar CSV', 'importacionescsv'));
echo "</button>";
echo "</p>";
echo "</form>";
echo "</div>";

// Pie estandar de GLPI.
Html::footer();
