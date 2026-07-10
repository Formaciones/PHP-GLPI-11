<?php

/**
 * Archivo de hooks complementarios del plugin.
 *
 * En GLPI, hook.php se usa para declarar funciones que GLPI puede invocar
 * durante eventos concretos.
 */

/**
 * Devuelve relaciones de base de datos conocidas por el plugin.
 */
function plugin_formaciones_getDatabaseRelations()
{
    // Las formaciones guardan una clave externa hacia el inventario de equipos.
    return [
        Computer::getTable() => [
            PluginFormacionesFormacion::getTable() => 'computers_id'
        ]
    ];
}
