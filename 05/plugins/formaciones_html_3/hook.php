<?php

function plugin_formaciones_getDatabaseRelations()
{

    return [
        Computer::getTable() => [
            PluginFormacionesFormacion::getTable() => 'computers_id'
        ]
    ];
}
