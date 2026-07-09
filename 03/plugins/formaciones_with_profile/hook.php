<?php

/**
 * Archivo de hooks complementarios del plugin.
 *
 * En GLPI, hook.php se usa para declarar funciones que GLPI puede invocar
 * durante eventos concretos. En este plugin no hay relaciones entre tablas,
 * pero dejamos la funcion preparada como ejemplo didactico.
 */

/**
 * Devuelve relaciones de base de datos conocidas por el plugin.
 */
function plugin_formaciones_getDatabaseRelations()
{
    // El plugin solo tiene una tabla independiente, sin relaciones externas.
    return [];
}
