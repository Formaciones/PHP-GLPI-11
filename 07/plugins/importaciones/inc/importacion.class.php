<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Entrada de Administracion para importar formaciones desde una API externa.
 */
class PluginImportacionesImportacion extends CommonGLPI
{
    // Usa el mismo derecho que el plugin Formaciones.
    public static $rightname = 'computer';

    // API externa que devuelve las formaciones en formato JSON.
    public const API_URL = 'http://host.docker.internal/labs/07/api/formaciones/';

    // Tabla creada por el plugin Formaciones. Este plugin no crea tabla propia.
    public const FORMACIONES_TABLE = 'glpi_plugin_formaciones_formaciones';

    /**
     * Nombre singular/plural mostrado por GLPI.
     */
    public static function getTypeName($nb = 0)
    {
        return _n('Importacion', 'Importaciones', $nb, 'importaciones');
    }

    /**
     * Nombre visible en el menu.
     */
    public static function getMenuName()
    {
        return __('Importaciones', 'importaciones');
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
     * Icono Tabler de subida.
     */
    public static function getIcon()
    {
        return 'ti ti-upload';
    }

    /**
     * URL de la pagina principal del plugin.
     */
    public static function getPageUrl($full = true)
    {
        global $CFG_GLPI;

        $url = '/plugins/importaciones/front/importacion.php';

        if ($full) {
            return $CFG_GLPI['root_doc'] . $url;
        }

        return $url;
    }

    /**
     * Descarga los datos de la API y los inserta en la tabla del plugin Formaciones.
     */
    public static function importFromApi()
    {
        global $DB;

        // Antes de insertar, comprobamos que el plugin Formaciones haya creado su tabla.
        if (!$DB->tableExists(self::FORMACIONES_TABLE)) {
            throw new RuntimeException(__('La tabla de formaciones no existe. Instala o activa primero el plugin Formaciones.', 'importaciones'));
        }

        // Obtiene el texto JSON devuelto por la API externa.
        $payload = self::fetchApiPayload();

        // Convierte el JSON en un array asociativo de PHP.
        $records = json_decode($payload, true);

        // Si la respuesta no es JSON valido o no es una lista, no se importa nada.
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($records)) {
            throw new RuntimeException(__('La API no ha devuelto un JSON valido.', 'importaciones'));
        }

        $inserted = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($records as $record) {
            // Cada elemento esperado debe ser un array con name y description.
            if (!is_array($record)) {
                continue;
            }

            // El nombre es obligatorio en la tabla de formaciones.
            $name = trim((string) ($record['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            // La descripcion puede venir vacia, pero se normaliza como texto.
            $description = trim((string) ($record['description'] ?? ''));

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

        return $inserted;
    }

    /**
     * Obtiene el cuerpo de respuesta de la API externa.
     */
    private static function fetchApiPayload()
    {
        // cURL permite definir timeouts para que la pantalla no quede bloqueada.
        if (function_exists('curl_init')) {
            $curl = curl_init(self::API_URL);
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_HTTPHEADER     => ['Accept: application/json']
            ]);

            $payload = curl_exec($curl);
            $error = curl_error($curl);
            $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            curl_close($curl);

            // Solo se aceptan respuestas HTTP correctas.
            if ($payload === false || $status < 200 || $status >= 300) {
                throw new RuntimeException(sprintf(
                    __('No se pudo conectar con la API externa. Estado HTTP: %s. %s', 'importaciones'),
                    $status ?: __('sin respuesta', 'importaciones'),
                    $error
                ));
            }

            return $payload;
        }

        // Alternativa simple si cURL no esta disponible en PHP.
        $payload = @file_get_contents(self::API_URL);

        if ($payload === false) {
            throw new RuntimeException(__('No se pudo conectar con la API externa.', 'importaciones'));
        }

        return $payload;
    }
}
