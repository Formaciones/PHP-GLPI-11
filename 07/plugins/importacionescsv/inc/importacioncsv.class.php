<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Entrada de Administracion para importar formaciones desde un fichero CSV.
 */
class PluginImportacionescsvImportacioncsv extends CommonGLPI
{
    // Usa el mismo derecho que el plugin Formaciones.
    public static $rightname = 'computer';

    // Tabla creada por el plugin Formaciones. Este plugin no crea tabla propia.
    public const FORMACIONES_TABLE = 'glpi_plugin_formaciones_formaciones';

    /**
     * Nombre singular/plural mostrado por GLPI.
     */
    public static function getTypeName($nb = 0)
    {
        return _n('Importacion CSV', 'Importaciones CSV', $nb, 'importacionescsv');
    }

    /**
     * Nombre visible en el menu.
     */
    public static function getMenuName()
    {
        return __('Importaciones CSV', 'importacionescsv');
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
     * Icono Tabler de subida de archivo.
     */
    public static function getIcon()
    {
        return 'ti ti-file-upload';
    }

    /**
     * URL de la pagina principal del plugin.
     */
    public static function getPageUrl($full = true)
    {
        global $CFG_GLPI;

        $url = '/plugins/importacionescsv/front/importacioncsv.php';

        if ($full) {
            return $CFG_GLPI['root_doc'] . $url;
        }

        return $url;
    }

    /**
     * Lee el CSV subido desde el formulario y guarda sus filas como formaciones.
     */
    public static function importFromUploadedCsv(array $file)
    {
        global $DB;

        // Antes de insertar, comprobamos que el plugin Formaciones haya creado su tabla.
        if (!$DB->tableExists(self::FORMACIONES_TABLE)) {
            throw new RuntimeException(__('La tabla de formaciones no existe. Instala o activa primero el plugin Formaciones.', 'importacionescsv'));
        }

        // PHP informa de cualquier problema de subida mediante el campo error.
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException(__('No se ha podido subir el fichero CSV.', 'importacionescsv'));
        }

        // El fichero temporal es el que PHP deja disponible tras recibir el formulario.
        $tmp_name = $file['tmp_name'] ?? '';
        if ($tmp_name === '' || !is_uploaded_file($tmp_name)) {
            throw new RuntimeException(__('El fichero CSV subido no es valido.', 'importacionescsv'));
        }

        $handle = fopen($tmp_name, 'r');
        if ($handle === false) {
            throw new RuntimeException(__('No se ha podido leer el fichero CSV.', 'importacionescsv'));
        }

        try {
            // La primera fila debe contener las cabeceras: name,description.
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new RuntimeException(__('El fichero CSV esta vacio.', 'importacionescsv'));
            }

            $columns = self::normalizeHeaders($headers);
            if (!isset($columns['name'], $columns['description'])) {
                throw new RuntimeException(__('El CSV debe tener las columnas name y description.', 'importacionescsv'));
            }

            $inserted = 0;
            $now = date('Y-m-d H:i:s');

            while (($row = fgetcsv($handle)) !== false) {
                // Si la fila esta completamente vacia, se ignora.
                if (self::isEmptyRow($row)) {
                    continue;
                }

                // El nombre es obligatorio en la tabla de formaciones.
                $name = trim((string) ($row[$columns['name']] ?? ''));
                if ($name === '') {
                    continue;
                }

                // La descripcion puede venir vacia. Si contiene comas sin comillas,
                // fgetcsv separa el texto en varias posiciones y las unimos de nuevo.
                $description = self::getDescriptionFromRow($row, $columns['description']);

                // Inserta el registro directamente en la tabla del plugin Formaciones.
                $result = $DB->insert(self::FORMACIONES_TABLE, [
                    'name'          => $name,
                    'description'   => $description,
                    'state'         => 1,
                    'date_creation' => $now,
                    'date_mod'      => $now
                ]);

                if ($result) {
                    $inserted++;
                }
            }
        } finally {
            fclose($handle);
        }

        return $inserted;
    }

    /**
     * Convierte la cabecera del CSV en un mapa nombre_columna => posicion.
     */
    private static function normalizeHeaders(array $headers)
    {
        $columns = [];

        foreach ($headers as $index => $header) {
            // Se eliminan espacios y BOM UTF-8 para aceptar CSV guardados desde editores comunes.
            $name = trim((string) $header);
            $name = preg_replace('/^\xEF\xBB\xBF/', '', $name);
            $columns[strtolower($name)] = $index;
        }

        return $columns;
    }

    /**
     * Indica si una fila no contiene ningun valor util.
     */
    private static function isEmptyRow(array $row)
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Recupera la descripcion de una fila.
     */
    private static function getDescriptionFromRow(array $row, $description_index)
    {
        $description_index = (int) $description_index;

        if (!array_key_exists($description_index, $row)) {
            return '';
        }

        // En el CSV del laboratorio la descripcion es la ultima columna logica.
        // Si el texto trae comas sin comillas, se recomponen los fragmentos.
        return trim(implode(',', array_slice($row, $description_index)));
    }
}
