<?php


if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}


class PluginFormacionesTipoFormacion extends CommonDropdown
{
    public static $rightname = 'dropdown';

    public static function getTypeName($nb = 0)
    {
        return _n('Tipo - Formaciones', 'Tipos - Formaciones', $nb, 'formaciones');
    }


    public static function getTable($classname = null)
    {
        return 'glpi_plugin_formaciones_tipoformacions';
    }
}

