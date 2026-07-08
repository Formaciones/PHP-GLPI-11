<?php


define('PLUGIN_SUBCONTRACTINSTALL_VERSION', '2.0.0');


function plugin_init_subcontractinstall()
{
    global $PLUGIN_HOOKS;


    $PLUGIN_HOOKS['csrf_compliant']['subcontractinstall'] = true;


    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/hook.php';


    $PLUGIN_HOOKS['item_add']['subcontractinstall'] = [
        'Computer' => 'plugin_subcontractinstall_computer_added'
    ];

    $PLUGIN_HOOKS['item_update']['subcontractinstall'] = [
        'Computer' => 'plugin_subcontractinstall_computer_updated'
    ];
}


function plugin_version_subcontractinstall() {
    return [
        'name'           => 'Subcontract Install',
        'version'        => PLUGIN_SUBCONTRACTINSTALL_VERSION,
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


function plugin_subcontractinstall_install()
{
    return true;
}


function plugin_subcontractinstall_uninstall()
{
    return true;
}


function plugin_subcontractinstall_check_prerequisites() 
{
    return true;
}


function plugin_subcontractinstall_check_config() 
{
    return true;
}
