<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Entrada de Administracion para exportar formaciones a un fichero CSV.
 */
class PluginExportacionesExportacion extends CommonGLPI
{
    // Usa el mismo derecho que el plugin Formaciones.
    public static $rightname = 'config';

    // Tabla creada por el plugin Formaciones. Este plugin solo la lee.
    public const FORMACIONES_TABLE = 'glpi_plugin_formaciones_formaciones';

    /**
     * Nombre singular/plural mostrado por GLPI.
     */
    public static function getTypeName($nb = 0)
    {
        return _n('Exportacion CSV', 'Exportaciones CSV', $nb, 'exportaciones');
    }

    /**
     * Nombre visible en el menu.
     */
    public static function getMenuName()
    {
        return __('Exportaciones CSV', 'exportaciones');
    }

    /**
     * Define la entrada del menu Administracion.
     */
    public static function getMenuContent()
    {
        $menu = [];

        // Texto que vera el usuario en el menu.
        $menu['title'] = self::getMenuName();

        // Pagina que se abrira al pulsar la opcion.
        $menu['page'] = self::getPageUrl(false);

        // Icono Tabler usado por GLPI.
        $menu['icon'] = self::getIcon();

        return $menu;
    }

    /**
     * Icono Tabler de descarga de archivo.
     */
    public static function getIcon()
    {
        return 'ti ti-file-download';
    }

    /**
     * URL de la pagina principal del plugin.
     */
    public static function getPageUrl($full = true)
    {
        global $CFG_GLPI;

        $url = '/plugins/exportaciones/front/exportacion.php';

        if ($full) {
            return $CFG_GLPI['root_doc'] . $url;
        }

        return $url;
    }

    /**
     * Genera y envia el CSV al navegador.
     */
    public static function exportToCsv()
    {
        global $DB;

        // Antes de exportar, comprobamos que el plugin Formaciones haya creado su tabla.
        if (!$DB->tableExists(self::FORMACIONES_TABLE)) {
            throw new RuntimeException(__('La tabla de formaciones no existe. Instala o activa primero el plugin Formaciones.', 'exportaciones'));
        }

        // Nombre del fichero descargado por el usuario.
        $filename = 'formaciones_' . date('Ymd_His') . '.csv';

        // Cabeceras HTTP para indicar al navegador que debe descargar un CSV.
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 para que Excel abra correctamente acentos y caracteres especiales.
        fwrite($output, "\xEF\xBB\xBF");

        // Primera fila del CSV: cabeceras de columnas.
        fputcsv($output, [
            'id',
            'name',
            'description',
            'state',
            'date_creation',
            'date_mod'
        ]);

        // Lee los registros ordenados igual que el listado de Formaciones.
        $rows = $DB->request([
            'FROM'  => self::FORMACIONES_TABLE,
            'ORDER' => ['name', 'id']
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['description'],
                $row['state'],
                $row['date_creation'],
                $row['date_mod']
            ]);
        }

        fclose($output);
        exit;
    }
}
