<?php

/**
 * Archivo principal del plugin.
 *
 * GLPI carga este fichero para descubrir el plugin, conocer su version,
 * registrar clases, anadir entradas de menu e instalar/desinstalar la base
 * de datos necesaria.
 */

// Constante usada por GLPI para mostrar la version e identificar migraciones.
define('PLUGIN_FORMACIONES_VERSION', '3.0.1');

/**
 * Inicializa el plugin cada vez que GLPI carga sus plugins activos.
 */
function plugin_init_formaciones()
{
    // Array global donde GLPI espera que los plugins registren sus hooks.
    global $PLUGIN_HOOKS;

    // Indica a GLPI que este plugin cumple con la proteccion CSRF.
    $PLUGIN_HOOKS['csrf_compliant']['formaciones'] = true;

    // Cuando cambia el perfil activo, inicializamos permisos del plugin.
    $PLUGIN_HOOKS['change_profile']['formaciones'] = ['PluginFormacionesProfile', 'initProfile'];

    // Anade los objetos del plugin dentro del menu Activos.
    // GLPI creara entradas para Formaciones e Instructores bajo esa seccion.
    $PLUGIN_HOOKS['menu_toadd']['formaciones'] = [
        'assets' => [
            'PluginFormacionesFormacion',
            'PluginFormacionesInstructor'
        ]
    ];

    // Registra la clase principal del objeto gestionado por el plugin.
    Plugin::registerClass('PluginFormacionesFormacion', ['addtabon' => []]);

    // Registra el objeto Instructor, relacionado desde Formaciones.
    Plugin::registerClass('PluginFormacionesInstructor', ['addtabon' => []]);

    // Registra el desplegable Tipo - Formaciones.
    // La clase se usa desde Configuracion > Desplegables y desde el formulario.
    Plugin::registerClass('PluginFormacionesTipoFormacion');

    // Registra la clase auxiliar encargada de instalar permisos.
    Plugin::registerClass('PluginFormacionesProfile');
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
 * Instala el plugin: crea la tabla y registra permisos.
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
    // Este bloque se ejecuta en instalaciones limpias del plugin.
    if (!$DB->tableExists($table)) {
        // En GLPI 11 se evita queryOrDie(); doQuery se usa aqui para DDL.
        // La tabla guarda tambien las claves de Tipo e Instructor relacionados.
        $DB->doQuery("CREATE TABLE `$table` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL DEFAULT '',
            `plugin_formaciones_tipoformacions_id` int unsigned NOT NULL DEFAULT 0,
            `plugin_formaciones_instructors_id` int unsigned NOT NULL DEFAULT 0,
            `description` text DEFAULT NULL,
            `state` int NOT NULL DEFAULT 1,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `plugin_formaciones_tipoformacions_id` (`plugin_formaciones_tipoformacions_id`),
            KEY `plugin_formaciones_instructors_id` (`plugin_formaciones_instructors_id`),
            KEY `state` (`state`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    } else {
        // Si la tabla ya existia, anadimos solo las columnas nuevas.
        // Esto permite actualizar el plugin sin borrar formaciones existentes.
        if (!$DB->fieldExists($table, 'plugin_formaciones_tipoformacions_id')) {
            $DB->doQuery("ALTER TABLE `$table`
                ADD `plugin_formaciones_tipoformacions_id` int unsigned NOT NULL DEFAULT 0 AFTER `name`,
                ADD KEY `plugin_formaciones_tipoformacions_id` (`plugin_formaciones_tipoformacions_id`)");
        }

        // Segunda columna nueva: instructor relacionado.
        if (!$DB->fieldExists($table, 'plugin_formaciones_instructors_id')) {
            $DB->doQuery("ALTER TABLE `$table`
                ADD `plugin_formaciones_instructors_id` int unsigned NOT NULL DEFAULT 0 AFTER `plugin_formaciones_tipoformacions_id`,
                ADD KEY `plugin_formaciones_instructors_id` (`plugin_formaciones_instructors_id`)");
        }
    }

    // Tabla propia del objeto Instructor.
    $instructor_table = 'glpi_plugin_formaciones_instructors';

    // Crea la tabla de instructores si no existe.
    if (!$DB->tableExists($instructor_table)) {
        // El campo name contiene apellidos porque GLPI lo usa como etiqueta base.
        // firstname y registration_number completan los campos pedidos.
        $DB->doQuery("CREATE TABLE `$instructor_table` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL DEFAULT '',
            `firstname` varchar(255) NOT NULL DEFAULT '',
            `registration_number` varchar(255) NOT NULL DEFAULT '',
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `firstname` (`firstname`),
            KEY `registration_number` (`registration_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    // Tabla del desplegable Tipo - Formaciones.
    $type_table = 'glpi_plugin_formaciones_tipoformacions';

    // Crea la tabla de tipos si no existe.
    if (!$DB->tableExists($type_table)) {
        // CommonDropdown espera name y comment; entities_id e is_recursive
        // permiten que GLPI gestione valores por entidad.
        $DB->doQuery("CREATE TABLE `$type_table` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `entities_id` int unsigned NOT NULL DEFAULT 0,
            `is_recursive` tinyint NOT NULL DEFAULT 0,
            `name` varchar(255) NOT NULL DEFAULT '',
            `comment` text DEFAULT NULL,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `name` (`name`),
            KEY `entities_id` (`entities_id`),
            KEY `is_recursive` (`is_recursive`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    // Instala los permisos relacionados con perfiles de usuario.
    PluginFormacionesProfile::install($migration);

    // Ejecuta la migracion registrada por GLPI.
    $migration->executeMigration();

    // true indica a GLPI que la instalacion termino correctamente.
    return true;
}

/**
 * Desinstala el plugin: elimina permisos y tabla propia.
 */
function plugin_formaciones_uninstall()
{
    // Objeto de base de datos de GLPI.
    global $DB;

    // Migration agrupa las operaciones de desinstalacion.
    $migration = new Migration(PLUGIN_FORMACIONES_VERSION);

    // Lista de tablas propias del plugin que se eliminaran al desinstalar.
    $tables = [
        'glpi_plugin_formaciones_formaciones',
        'glpi_plugin_formaciones_instructors',
        'glpi_plugin_formaciones_tipoformacions'
    ];

    // Borra los permisos creados durante la instalacion.
    PluginFormacionesProfile::uninstall($migration);

    // Si las tablas existen, se eliminan al desinstalar el plugin.
    foreach ($tables as $table) {
        if ($DB->tableExists($table)) {
            $DB->dropTable($table);
        }
    }

    // Ejecuta la migracion registrada por GLPI.
    $migration->executeMigration();

    // true indica a GLPI que la desinstalacion termino correctamente.
    return true;
}
