<?php

// Constante con la versión
define('PLUGIN_SUBCONTRACTINSTALL_VERSION', '1.0.10');

// Metadatos de plugin visibles en el portal de GLPI
function plugin_version_subcontractinstall()
{
    return [
        'name'         => 'Subcontrata Instalación',
        'version'      => PLUGIN_SUBCONTRACTINSTALL_VERSION,
        'author'       => 'Borja Cabeza',
        'license'      => 'GLP v2+',
        'homepage'     => '',
        'requirements' => [
            'glpi' => [
                'min' => '11.0.0',
                'max' => '11.9.9'
            ]
        ]
    ];
}

// Función que se ejecuta al cargar el plugins
function plugin_init_subcontractinstall()
{
    global $PLUGIN_HOOKS;

    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/hook.php';

    // Indica protección CSRF para el plugin
    $PLUGIN_HOOKS['csrf_compliant']['subcontractinstall'] = true;

    /*
    $PLUGIN_HOOKS['item_add']['subcontractinstall'] = [
        'Cumputer' => '<nombre de función que se se ejecuta>'
    ];
    */

    $PLUGIN_HOOKS['item_add']['subcontractinstall'] = [
        'Computer' => 'plugin_subcontractinstall_computer_added'
    ];

    $PLUGIN_HOOKS['item_update']['subcontractinstall'] = [
        'Computer' => 'plugin_subcontractinstall_computer_updated'
    ];   
}

// Instalación Plugins (creación tablas; pre cargar de datos; permisos; etc.)
function plugin_subcontractinstall_install()
{
    return true;
}

// Desinstalación Plugins (eliminar tablas; salvar datos; eliminar permisos; etc.)
function plugin_subcontractinstall_uninstall()
{
    return true;
}

// Validación previa a la instalación
function plugin_subcontractinstall_check_prerequisites()
{
    return true;
}

// Valiadación previa a la activación
function plugin_subcontractinstall_check_config()
{
    return true;
}
