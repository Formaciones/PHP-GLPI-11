<?php

// Hooks contiene los nombres oficiales de los puntos de extension de GLPI 11.
use Glpi\Plugin\Hooks;

/**
 * Archivo principal del plugin.
 *
 * GLPI carga este fichero para descubrir el plugin, conocer su version,
 * registrar clases, anadir entradas de menu e instalar/desinstalar la base
 * de datos necesaria.
 */

// Constante usada por GLPI para mostrar la version e identificar migraciones.
define('PLUGIN_FORMACIONES_VERSION', '6.0.2');

/**
 * Inicializa el plugin cada vez que GLPI carga sus plugins activos.
 */
function plugin_init_formaciones()
{
    // Array global donde GLPI espera que los plugins registren sus hooks.
    global $PLUGIN_HOOKS;

    // Indica a GLPI que este plugin cumple con la proteccion CSRF.
    $PLUGIN_HOOKS['csrf_compliant']['formaciones'] = true;

    // Anade el objeto Formaciones dentro del menu Activos.
    $PLUGIN_HOOKS['menu_toadd']['formaciones'] = ['assets' => 'PluginFormacionesFormacion'];

    // Carga la hoja de estilos propia despues de los estilos nativos de GLPI.
    // Las reglas se limitan a .plugin-formaciones para no afectar otros formularios.
    $PLUGIN_HOOKS[Hooks::ADD_CSS]['formaciones'] = 'css/formaciones.css';

    // Carga el JavaScript propio que valida las fechas en el navegador.
    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['formaciones'] = [
        'js/formaciones.js'
    ];

    // Registra la clase principal del objeto gestionado por el plugin.
    Plugin::registerClass('PluginFormacionesFormacion', [
        'addtabon' => []
    ]);

}

/**
 * Devuelve los metadatos que GLPI muestra en Configuracion > Plugins.
 */
function plugin_version_formaciones()
{
    return [
        // Nombre visible del plugin en la interfaz.
        'name'           => __('Formaciones', 'formaciones'),
        // Version declarada en la constante superior.
        'version'        => PLUGIN_FORMACIONES_VERSION,
        // Autor mostrado por GLPI.
        'author'         => 'Borja',
        // Licencia informativa del plugin.
        'license'        => 'GPL v2+',
        // Pagina web del plugin si existiera.
        'homepage'       => '',
        // Rango de versiones de GLPI compatible con este plugin.
        'requirements'   => [
            'glpi' => [
                'min' => '11.0.0',
                'max' => '11.9.9'
            ]
        ]
    ];
}

/**
 * Comprueba requisitos antes de permitir instalar o activar el plugin.
 */
function plugin_formaciones_check_prerequisites()
{
    // Evita instalarlo en versiones anteriores a GLPI 11.
    if (version_compare(GLPI_VERSION, '11.0.0', 'lt')) {
        echo 'This plugin requires GLPI >= 11.0.0';
        return false;
    }

    // Si se llega aqui, los requisitos minimos se cumplen.
    return true;
}

/**
 * Comprueba si la configuracion del plugin permite activarlo.
 */
function plugin_formaciones_check_config()
{
    // Este plugin no necesita parametros previos de configuracion.
    return true;
}

/**
 * Instala el plugin: crea la tabla propia.
 */
function plugin_formaciones_install()
{
    // Objeto de base de datos de GLPI.
    global $DB;

    // Migration permite agrupar operaciones de instalacion.
    $migration = new Migration(PLUGIN_FORMACIONES_VERSION);

    // Nombre de la tabla propia del plugin.
    $table = 'glpi_plugin_formaciones_formaciones';

    // Solo crea la tabla si aun no existe.
    if (!$DB->tableExists($table)) {
        // En GLPI 11 se evita queryOrDie(); doQuery se usa aqui para DDL.
        $DB->doQuery("CREATE TABLE `$table` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL DEFAULT '',
            `description` text DEFAULT NULL,
            `state` int NOT NULL DEFAULT 1,
            `begin_date` date DEFAULT NULL,
            `end_date` date DEFAULT NULL,
            `number_places` int unsigned NOT NULL DEFAULT 0,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `state` (`state`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } else {
        // Las siguientes operaciones actualizan una instalacion de un laboratorio anterior.
        if (!$DB->fieldExists($table, 'begin_date')) {
            // Fecha prevista para el inicio de la formacion.
            $migration->addField($table, 'begin_date', 'date', ['after' => 'state']);
        }

        if (!$DB->fieldExists($table, 'end_date')) {
            // Fecha prevista para la finalizacion de la formacion.
            $migration->addField($table, 'end_date', 'date', ['after' => 'begin_date']);
        }

        if (!$DB->fieldExists($table, 'number_places')) {
            // Numero de alumnos que pueden inscribirse; nunca puede ser negativo.
            $migration->addField($table, 'number_places', 'integer', [
                'after'    => 'end_date',
                'unsigned' => true,
                'default'  => 0
            ]);
        }
    }

    // Ejecuta la migracion registrada por GLPI.
    $migration->executeMigration();

    // true indica a GLPI que la instalacion termino correctamente.
    return true;
}

/**
 * Desinstala el plugin: elimina la tabla propia.
 */
function plugin_formaciones_uninstall()
{
    // Objeto de base de datos de GLPI.
    global $DB;

    // Migration agrupa las operaciones de desinstalacion.
    $migration = new Migration(PLUGIN_FORMACIONES_VERSION);

    // Tabla que almacena las formaciones.
    $table = 'glpi_plugin_formaciones_formaciones';

    // Si la tabla existe, se elimina al desinstalar el plugin.
    if ($DB->tableExists($table)) {
        $DB->dropTable($table);
    }

    // Ejecuta la migracion registrada por GLPI.
    $migration->executeMigration();

    // true indica a GLPI que la desinstalacion termino correctamente.
    return true;
}
