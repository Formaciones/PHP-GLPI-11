<?php

/**
 * Archivo principal del plugin Importaciones CSV.
 *
 * Registra una entrada en el menu Administracion y declara los metadatos que
 * GLPI muestra en Configuracion > Plugins.
 */

define('PLUGIN_IMPORTACIONESCSV_VERSION', '7.0.1');

/**
 * Inicializa el plugin cada vez que GLPI carga sus plugins activos.
 */
function plugin_init_importacionescsv()
{
    global $PLUGIN_HOOKS;

    // Indica a GLPI que el plugin incluye proteccion CSRF en sus formularios.
    $PLUGIN_HOOKS['csrf_compliant']['importacionescsv'] = true;

    // Anade la opcion Importaciones CSV dentro del menu Administracion.
    $PLUGIN_HOOKS['menu_toadd']['importacionescsv'] = [
        'admin' => 'PluginImportacionescsvImportacioncsv'
    ];

    Plugin::registerClass('PluginImportacionescsvImportacioncsv');
}

/**
 * Devuelve los metadatos visibles en la pantalla de plugins.
 */
function plugin_version_importacionescsv()
{
    return [
        'name'         => __('Importaciones CSV', 'importacionescsv'),
        'version'      => PLUGIN_IMPORTACIONESCSV_VERSION,
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

/**
 * Comprueba requisitos antes de instalar o activar.
 */
function plugin_importacionescsv_check_prerequisites()
{
    if (version_compare(GLPI_VERSION, '11.0.0', 'lt')) {
        echo 'This plugin requires GLPI >= 11.0.0';
        return false;
    }

    return true;
}

/**
 * Comprueba si la configuracion permite activar el plugin.
 */
function plugin_importacionescsv_check_config()
{
    return true;
}

/**
 * El plugin no necesita tabla propia: reutiliza la tabla de Formaciones.
 */
function plugin_importacionescsv_install()
{
    $migration = new Migration(PLUGIN_IMPORTACIONESCSV_VERSION);
    $migration->executeMigration();

    return true;
}

/**
 * No hay datos propios que eliminar al desinstalar.
 */
function plugin_importacionescsv_uninstall()
{
    $migration = new Migration(PLUGIN_IMPORTACIONESCSV_VERSION);
    $migration->executeMigration();

    return true;
}
