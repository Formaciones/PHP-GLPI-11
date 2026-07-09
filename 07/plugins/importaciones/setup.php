<?php

/**
 * Archivo principal del plugin Importaciones.
 *
 * Registra una entrada en el menu Administracion y declara los metadatos que
 * GLPI muestra en Configuracion > Plugins.
 */

define('PLUGIN_IMPORTACIONES_VERSION', '7.0.1');

/**
 * Inicializa el plugin cada vez que GLPI carga sus plugins activos.
 */
function plugin_init_importaciones()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['importaciones'] = true;

    // Anade la opcion Importaciones dentro del menu Administracion.
    $PLUGIN_HOOKS['menu_toadd']['importaciones'] = [
        'admin' => 'PluginImportacionesImportacion'
    ];

    Plugin::registerClass('PluginImportacionesImportacion');
}

/**
 * Devuelve los metadatos visibles en la pantalla de plugins.
 */
function plugin_version_importaciones()
{
    return [
        'name'         => __('Importaciones', 'importaciones'),
        'version'      => PLUGIN_IMPORTACIONES_VERSION,
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
function plugin_importaciones_check_prerequisites()
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
function plugin_importaciones_check_config()
{
    return true;
}

/**
 * El plugin no necesita tabla propia.
 */
function plugin_importaciones_install()
{
    $migration = new Migration(PLUGIN_IMPORTACIONES_VERSION);
    $migration->executeMigration();

    return true;
}

/**
 * No hay datos propios que eliminar al desinstalar.
 */
function plugin_importaciones_uninstall()
{
    $migration = new Migration(PLUGIN_IMPORTACIONES_VERSION);
    $migration->executeMigration();

    return true;
}
