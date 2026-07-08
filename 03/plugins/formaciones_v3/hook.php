<?php




function plugin_formaciones_getDatabaseRelations()
{


    return [

        PluginFormacionesTipoFormacion::getTable() => [
            PluginFormacionesFormacion::getTable() => 'plugin_formaciones_tipoformacions_id'
        ],

        PluginFormacionesInstructor::getTable() => [
            PluginFormacionesFormacion::getTable() => 'plugin_formaciones_instructors_id'
        ]
    ];
}


function plugin_formaciones_getDropdown()
{

    return [
        PluginFormacionesTipoFormacion::class => PluginFormacionesTipoFormacion::getTypeName(2)
    ];
}

