<?php

/**
 * Archivo principal del plugin.
 *
 * GLPI carga este fichero para descubrir el plugin, conocer su version,
 * registrar clases, anadir entradas de menu e instalar/desinstalar la base
 * de datos necesaria.
 */

// Constante usada por GLPI para mostrar la version e identificar migraciones.
define('PLUGIN_FORMACIONES_VERSION', '5.2.0');

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
            `format` varchar(20) NOT NULL DEFAULT 'online',
            `trainer` varchar(100) NOT NULL DEFAULT '',
            `start_date` date DEFAULT NULL,
            `end_date` date DEFAULT NULL,
            `duration_hours` decimal(6,2) NOT NULL DEFAULT 0,
            `capacity` int NOT NULL DEFAULT 0,
            `cost` decimal(10,2) NOT NULL DEFAULT 0,
            `currency` varchar(3) NOT NULL DEFAULT 'EUR',
            `location` varchar(255) NOT NULL DEFAULT '',
            `meeting_url` varchar(255) NOT NULL DEFAULT '',
            `level` varchar(50) NOT NULL DEFAULT 'inicial',
            `target_audience` varchar(255) NOT NULL DEFAULT '',
            `certificate` tinyint NOT NULL DEFAULT 1,
            `observations` text DEFAULT NULL,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `state` (`state`),
            KEY `format` (`format`),
            KEY `trainer` (`trainer`),
            KEY `start_date` (`start_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    // Migracion para instalaciones existentes: anade los campos de demostracion
    // del formulario completo sin perder los datos que ya hubiese en la tabla.
    $fields_to_add = [
        'format'         => "`format` varchar(20) NOT NULL DEFAULT 'online' AFTER `state`",
        'trainer'        => "`trainer` varchar(100) NOT NULL DEFAULT '' AFTER `format`",
        'start_date'     => "`start_date` date DEFAULT NULL AFTER `trainer`",
        'end_date'       => "`end_date` date DEFAULT NULL AFTER `start_date`",
        'duration_hours' => "`duration_hours` decimal(6,2) NOT NULL DEFAULT 0 AFTER `end_date`",
        'capacity'       => "`capacity` int NOT NULL DEFAULT 0 AFTER `duration_hours`",
        'cost'           => "`cost` decimal(10,2) NOT NULL DEFAULT 0 AFTER `capacity`",
        'currency'       => "`currency` varchar(3) NOT NULL DEFAULT 'EUR' AFTER `cost`",
        'location'       => "`location` varchar(255) NOT NULL DEFAULT '' AFTER `currency`",
        'meeting_url'    => "`meeting_url` varchar(255) NOT NULL DEFAULT '' AFTER `location`",
        'level'          => "`level` varchar(50) NOT NULL DEFAULT 'inicial' AFTER `meeting_url`",
        'target_audience'=> "`target_audience` varchar(255) NOT NULL DEFAULT '' AFTER `level`",
        'certificate'    => "`certificate` tinyint NOT NULL DEFAULT 1 AFTER `target_audience`",
        'observations'   => "`observations` text DEFAULT NULL AFTER `certificate`"
    ];

    foreach ($fields_to_add as $field => $definition) {
        if ($DB->tableExists($table) && !$DB->fieldExists($table, $field)) {
            $DB->doQuery("ALTER TABLE `$table` ADD COLUMN $definition");
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
