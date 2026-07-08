<?php

define('PLUGIN_FORMACIONES_VERSION', '5.2.0');

function plugin_init_formaciones()
{

    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['formaciones'] = true;

    $PLUGIN_HOOKS['change_profile']['formaciones'] = ['PluginFormacionesProfile', 'initProfile'];

    $PLUGIN_HOOKS['menu_toadd']['formaciones'] = ['assets' => 'PluginFormacionesFormacion'];

    Plugin::registerClass('PluginFormacionesFormacion', [
        'addtabon' => []
    ]);

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

    PluginFormacionesProfile::install($migration);

    $migration->executeMigration();

    return true;
}

function plugin_formaciones_uninstall()
{

    global $DB;

    $migration = new Migration(PLUGIN_FORMACIONES_VERSION);

    $table = 'glpi_plugin_formaciones_formaciones';

    PluginFormacionesProfile::uninstall($migration);

    if ($DB->tableExists($table)) {
        $DB->dropTable($table);
    }

    $migration->executeMigration();

    return true;
}
