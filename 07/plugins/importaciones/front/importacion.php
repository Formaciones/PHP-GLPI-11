<?php

// Carga el entorno completo de GLPI: sesion, clases, DB, permisos, etc.
include('../../../inc/includes.php');

// Usa el mismo derecho que Formaciones. Si no hay permiso de lectura,
// GLPI detiene la ejecucion y muestra su mensaje estandar de permisos.
Session::checkRight(PluginImportacionesImportacion::$rightname, READ);

// La importacion se lanza con un parametro GET para evitar problemas con la
// validacion CSRF automatica de GLPI en formularios POST de plugins.
if (isset($_GET['import'])) {
    try {
        // Llama a la clase del plugin, que se encarga de pedir el JSON e insertarlo.
        $inserted = PluginImportacionesImportacion::importFromApi();

        // El mensaje se mostrara despues de redirigir al listado.
        Session::addMessageAfterRedirect(sprintf(
            __('Importacion completada. Registros insertados: %d', 'importaciones'),
            $inserted
        ));
    } catch (Throwable $e) {
        // Si falla la API, el JSON o la base de datos, se muestra el error al usuario.
        Session::addMessageAfterRedirect($e->getMessage(), false, ERROR);
    }

    // Tras importar, se envia al usuario al listado del plugin Formaciones.
    Html::redirect(PluginFormacionesFormacion::getSearchURL(false));
}

// URL que ejecuta la importacion. Se construye desde la clase para no depender
// de $_SERVER['PHP_SELF'], que puede cambiar segun la configuracion de GLPI.
$import_url = PluginImportacionesImportacion::getPageUrl(false) . '?import=1';

// Cabecera estandar de GLPI. La entrada esta en el menu Administracion.
Html::header(
    PluginImportacionesImportacion::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'admin',
    PluginImportacionesImportacion::class
);

// Contenido sencillo de la pagina: un unico boton para iniciar la carga.
echo "<div class='center'>";
echo "<br />";
echo "<h1>Importar datos de formaciones</h1><hr />";
echo "<p>";
echo "<a class='btn btn-primary' href='" . htmlescape($import_url) . "'>";
echo htmlescape(__('Importar datos', 'importaciones'));
echo "</a>";
echo "</p>";
echo "</div>";

// Pie estandar de GLPI.
Html::footer();
