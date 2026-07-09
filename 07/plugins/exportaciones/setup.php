<?php

/**
 * Archivo principal del plugin Exportaciones CSV.
 *
 * Registra una entrada en el menu Administracion y declara los metadatos que
 * GLPI muestra en Configuracion > Plugins.
 */

define('PLUGIN_EXPORTACIONES_VERSION', '7.0.0');

/**
 * Inicializa el plugin cada vez que GLPI carga sus plugins activos.
 */
function plugin_init_exportaciones()
{
    global $PLUGIN_HOOKS;

    // La descarga se lanza con GET y no modifica datos.
    $PLUGIN_HOOKS['csrf_compliant']['exportaciones'] = true;

    // Anade la opcion Exportaciones CSV dentro del menu Administracion.
    $PLUGIN_HOOKS['menu_toadd']['exportaciones'] = [
        'admin' => 'PluginExportacionesExportacion'
    ];

    Plugin::registerClass('PluginExportacionesExportacion');
}

/**
 * Devuelve los metadatos visibles en la pantalla de plugins.
 */
function plugin_version_exportaciones()
{
    return [
        'name'         => __('Exportaciones CSV', 'exportaciones'),
        'version'      => PLUGIN_EXPORTACIONES_VERSION,
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
function plugin_exportaciones_check_prerequisites()
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
function plugin_exportaciones_check_config()
{
    return true;
}

/**
 * El plugin no necesita tabla propia: lee la tabla de Formaciones.
 */
function plugin_exportaciones_install()
{
    $migration = new Migration(PLUGIN_EXPORTACIONES_VERSION);
    $migration->executeMigration();

    return true;
}

/**
 * No hay datos propios que eliminar al desinstalar.
 */
function plugin_exportaciones_uninstall()
{
    $migration = new Migration(PLUGIN_EXPORTACIONES_VERSION);
    $migration->executeMigration();

    return true;
}
