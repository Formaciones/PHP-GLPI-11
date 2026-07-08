<?php




define('PLUGIN_EXTENSIONTICKETS_VERSION', '1.0.0');


function plugin_init_extensiontickets()
{

    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['extensiontickets'] = true;

    Plugin::registerClass('PluginExtensionticketsTicketExtension', [
        'addtabon' => ['Ticket']
    ]);
}


function plugin_version_extensiontickets()
{
    return [
        'name'         => __('Extension Tickets', 'extensiontickets'),
        'version'      => PLUGIN_EXTENSIONTICKETS_VERSION,
        'author'       => 'Borja',
        'license'      => 'GPL v2+',
        'homepage'     => '',
        'requirements' => [
            'glpi' => [
                'min' => '11.0.0',
                'max' => '11.9.9'
            ]
        ]
    ];
}


function plugin_extensiontickets_check_prerequisites()
{
    if (version_compare(GLPI_VERSION, '11.0.0', 'lt')) {
        echo 'This plugin requires GLPI >= 11.0.0';
        return false;
    }

    return true;
}


function plugin_extensiontickets_check_config()
{

    return true;
}


function plugin_extensiontickets_install()
{
    global $DB;
    $migration = new Migration(PLUGIN_EXTENSIONTICKETS_VERSION);
    $table = 'glpi_plugin_extensiontickets_ticketextensions';

    if (!$DB->tableExists($table)) {
        $DB->doQuery("CREATE TABLE `$table` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `tickets_id` int unsigned NOT NULL DEFAULT 0,
            `external_assignment` tinyint NOT NULL DEFAULT 0,
            `external_company` varchar(255) NOT NULL DEFAULT '',
            `cost` decimal(20,2) NOT NULL DEFAULT 0.00,
            `date_creation` timestamp NULL DEFAULT NULL,
            `date_mod` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `tickets_id` (`tickets_id`),
            KEY `external_assignment` (`external_assignment`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }


    $migration->executeMigration();

    return true;
}


function plugin_extensiontickets_uninstall()
{
    global $DB;
    $migration = new Migration(PLUGIN_EXTENSIONTICKETS_VERSION);
    $table = 'glpi_plugin_extensiontickets_ticketextensions';

    if ($DB->tableExists($table)) {
        $DB->dropTable($table);
    }

    $migration->executeMigration();

    return true;
}

