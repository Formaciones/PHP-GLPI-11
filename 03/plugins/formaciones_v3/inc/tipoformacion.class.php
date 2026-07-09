<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Desplegable mantenible desde Configuracion > Desplegables.
 *
 * CommonDropdown es la clase base de GLPI para tablas tipo "catalogo":
 * registros con nombre, comentario, entidad y recursividad. Al declarar esta
 * clase y su hook, GLPI permite mantener sus valores desde Configuracion.
 */
class PluginFormacionesTipoFormacion extends CommonDropdown
{
    // Derecho estandar de GLPI para administrar desplegables.
    public static $rightname = 'dropdown';

    /**
     * Nombre singular/plural visible en Configuracion > Desplegables.
     */
    public static function getTypeName($nb = 0)
    {
        return _n('Tipo - Formaciones', 'Tipos - Formaciones', $nb, 'formaciones');
    }

    /**
     * Tabla donde se guardan los valores del desplegable.
     */
    public static function getTable($classname = null)
    {
        return 'glpi_plugin_formaciones_tipoformacions';
    }

    /**
     * Opciones directas usadas como respaldo si el desplegable ajax de GLPI
     * no puede inicializar la clase del plugin.
     */
    public static function getDropdownOptions()
    {
        global $DB;

        $options = [];
        $iterator = $DB->request([
            'SELECT' => ['id', 'name'],
            'FROM'   => self::getTable(),
            'ORDER'  => ['name']
        ]);

        foreach ($iterator as $row) {
            $options[(int) $row['id']] = $row['name'];
        }

        return $options;
    }
}
