<?php


if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}


class PluginFormacionesProfile extends Profile
{
    public static $rightname = 'profile';

    // Instalación de permisos en base a perfiles existentes
    public static function install(Migration $migration)
    {
        // Representa la DB de GLPI
        global $DB;

        // Declaramos un permiso propio en GLPI
        ProfileRight::addProfileRights(['plugin_formaciones_formacion']);

        // Recuperamos todos los perfiles existentes en GLPI
        $profiles = $DB->request([
            'FROM' => Profile::getTable()
        ]);

        // Recorremos los perfiles y les asignamos el permiso
        foreach ($profiles as $profile) {
            if (($profile['interface'] ?? '') !== 'helpdesk') {
                self::addDefaultRights((int) $profile['id']);
            }
        }
    }

    // Eliminación de permisos en base a perfiles existentes
    public static function uninstall(Migration $migration)
    {
        // Eliminamos el permiso que se elimina de todos los perfiles
        ProfileRight::deleteProfileRights(['plugin_formaciones_formacion']);
    }

    // Inicializamos permisos del usuario activo
    public static function initProfile()
    {
        foreach (['plugin_formaciones_formacion'] as $right) {
            if (!isset($_SESSION['glpiactiveprofile'][$right])) {
                $_SESSION['glpiactiveprofile'][$right] = 0;
            }
        }
    }

    // Creación de permisos por defecto para un perfil concreto
    private static function addDefaultRights($profiles_id)
    {
        $right = new ProfileRight();

        if (!$right->getFromDBByCrit([
            'profiles_id' => $profiles_id,
            'name'        => 'plugin_formaciones_formacion'
        ])) {
            // ALLSTANDARDRIGHT = leer, actualizar, insertar y borrar
            $right->add([
                'profiles_id' => $profiles_id,
                'name'        => 'plugin_formaciones_formacion',
                'rights'      => ALLSTANDARDRIGHT
            ]);
        }
    }
}

