<?php

/**
 * Archivo de hooks complementarios del plugin.
 *
 * En GLPI, hook.php se usa para declarar funciones que GLPI puede invocar
 * durante eventos concretos. Aqui declaramos relaciones entre tablas y
 * desplegables propios del plugin.
 */

/**
 * Devuelve relaciones de base de datos conocidas por el plugin.
 */
function plugin_formaciones_getDatabaseRelations()
{
    // Cada clave del array es una tabla "padre" y dentro se indican las tablas
    // que guardan una clave externa hacia ella.
    return [
        // Las formaciones apuntan al desplegable Tipo - Formaciones.
        PluginFormacionesTipoFormacion::getTable() => [
            PluginFormacionesFormacion::getTable() => 'plugin_formaciones_tipoformacions_id'
        ],
        // Las formaciones apuntan tambien a un Instructor.
        PluginFormacionesInstructor::getTable() => [
            PluginFormacionesFormacion::getTable() => 'plugin_formaciones_instructors_id'
        ]
    ];
}

/**
 * Declara desplegables propios del plugin para Configuracion > Desplegables.
 */
function plugin_formaciones_getDropdown()
{
    // Este hook hace visible el desplegable en Configuracion > Desplegables.
    // GLPI agrupa este array bajo el nombre del plugin y, si el valor es null,
    // usa getTypeName() de la clase para mostrar la etiqueta.
    return [
        PluginFormacionesTipoFormacion::class => null
    ];
}
