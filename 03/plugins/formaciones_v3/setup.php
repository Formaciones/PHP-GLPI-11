<?php




define('PLUGIN_FORMACIONES_VERSION', '3.0.0');


function plugin_init_formaciones()
{
    global $PLUGIN_HOOKS;


    $PLUGIN_HOOKS['csrf_compliant']['formaciones'] = true;

    $PLUGIN_HOOKS['change_profile']['formaciones'] = ['PluginFormacionesProfile', 'initProfile'];

    $PLUGIN_HOOKS['menu_toadd']['formaciones'] = [
        'assets' => [
            'PluginFormacionesFormacion',
            'PluginFormacionesInstructor'
        ]
    ];

    Plugin::registerClass('PluginFormacionesFormacion', ['addtabon' => []]);
    Plugin::registerClass('PluginFormacionesInstructor', ['addtabon' => []]);
    Plugin::registerClass('PluginFormacionesTipoFormacion');
    Plugin::registerClass('PluginFormacionesProfile');
}


function plugin_version_formaciones()
{
    return [
        'name'           => __('Formaciones', 'formaciones'),
        'version'        => PLUGIN_FORMACIONES_VERSION,
        'author'         => 'Borja',
        'license'        => 'GPL v2+',
        'homepage'       => '',
        'requirements'   => [
            'glpi' => [
                'min' => '11.0.0',
                'max' => '11.9.9'
            ]
        ]
    ];
}


function plugin_formaciones_check_prerequisites()
{

    if (version_compare(GLPI_VERSION, '11.0.0', 'lt')) {
        echo 'This plugin requires GLPI >= 11.0.0';
        return false;
    }


    return true;
}


function plugin_formaciones_check_config()
{

    return true;
}


function plugin_formaciones_install()
{
    global $DB;

    $migration = new Migration(PLUGIN_FORMACIONES_VERSION);

    $table = 'glpi_plugin_formaciones_formaciones';

    if (!$DB->tableExists($table)) {
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
        if (!$DB->fieldExists($table, 'plugin_formaciones_tipoformacions_id')) {
            $DB->doQuery("ALTER TABLE `$table`
                ADD `plugin_formaciones_tipoformacions_id` int unsigned NOT NULL DEFAULT 0 AFTER `name`,
                ADD KEY `plugin_formaciones_tipoformacions_id` (`plugin_formaciones_tipoformacions_id`)");
        }

        if (!$DB->fieldExists($table, 'plugin_formaciones_instructors_id')) {
            $DB->doQuery("ALTER TABLE `$table`
                ADD `plugin_formaciones_instructors_id` int unsigned NOT NULL DEFAULT 0 AFTER `plugin_formaciones_tipoformacions_id`,
                ADD KEY `plugin_formaciones_instructors_id` (`plugin_formaciones_instructors_id`)");
        }
    }

    $instructor_table = 'glpi_plugin_formaciones_instructors';

    if (!$DB->tableExists($instructor_table)) {
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

    $type_table = 'glpi_plugin_formaciones_tipoformacions';

    if (!$DB->tableExists($type_table)) {
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

    PluginFormacionesProfile::install($migration);
    $migration->executeMigration();

    return true;
}


function plugin_formaciones_uninstall()
{
    global $DB;
    
    $migration = new Migration(PLUGIN_FORMACIONES_VERSION);

    $tables = [
        'glpi_plugin_formaciones_formaciones',
        'glpi_plugin_formaciones_instructors',
        'glpi_plugin_formaciones_tipoformacions'
    ];

    PluginFormacionesProfile::uninstall($migration);

    foreach ($tables as $table) {
        if ($DB->tableExists($table)) {
            $DB->dropTable($table);
        }
    }

    $migration->executeMigration();

    return true;
}

