<?php

define('PLUGIN_FORMACIONES_VERSION', '5.3.0');

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
            `computers_id` int unsigned NOT NULL DEFAULT 0,
            `description` text DEFAULT NULL,
            `state` int NOT NULL DEFAULT 1,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `computers_id` (`computers_id`),
            KEY `name` (`name`),
            KEY `state` (`state`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    if ($DB->tableExists($table) && !$DB->fieldExists($table, 'computers_id')) {
        $DB->doQuery("ALTER TABLE `$table`
            ADD COLUMN `computers_id` int unsigned NOT NULL DEFAULT 0 AFTER `name`,
            ADD KEY `computers_id` (`computers_id`)");
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
