<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Clase auxiliar para preparar permisos del plugin en los perfiles.
 *
 * Aunque el objeto principal usa el derecho "computer", se conserva esta
 * clase como ejemplo de como registrar un derecho propio de plugin.
 */
class PluginFormacionesProfile extends Profile
{
    // Este objeto trabaja sobre perfiles, por eso usa el derecho profile.
    public static $rightname = 'profile';

    /**
     * Instala derechos del plugin en los perfiles existentes.
     */
    public static function install(Migration $migration)
    {
        // Objeto de base de datos de GLPI.
        global $DB;

        // Declara el derecho propio para que GLPI lo conozca.
        ProfileRight::addProfileRights(['plugin_formaciones_formacion']);

        // Recupera todos los perfiles existentes.
        $profiles = $DB->request([
            'FROM' => Profile::getTable()
        ]);

        // Recorre perfiles para dar permisos por defecto a perfiles internos.
        foreach ($profiles as $profile) {
            // Los perfiles helpdesk se excluyen porque son de interfaz simplificada.
            if (($profile['interface'] ?? '') !== 'helpdesk') {
                self::addDefaultRights((int) $profile['id']);
            }
        }
    }

    /**
     * Elimina derechos del plugin durante la desinstalacion.
     */
    public static function uninstall(Migration $migration)
    {
        ProfileRight::deleteProfileRights(['plugin_formaciones_formacion']);
    }

    /**
     * Inicializa el permiso en la sesion del usuario activo.
     */
    public static function initProfile()
    {
        // GLPI guarda los permisos activos en $_SESSION['glpiactiveprofile'].
        foreach (['plugin_formaciones_formacion'] as $right) {
            // Si el permiso no existe en sesion, se inicia a 0.
            if (!isset($_SESSION['glpiactiveprofile'][$right])) {
                $_SESSION['glpiactiveprofile'][$right] = 0;
            }
        }
    }

    /**
     * Crea permisos por defecto para un perfil concreto.
     */
    private static function addDefaultRights($profiles_id)
    {
        // Objeto GLPI que representa un permiso de perfil.
        $right = new ProfileRight();

        // Evita duplicar el derecho si ya existe para ese perfil.
        if (!$right->getFromDBByCrit([
            'profiles_id' => $profiles_id,
            'name'        => 'plugin_formaciones_formacion'
        ])) {
            // ALLSTANDARDRIGHT concede lectura, alta, modificacion y borrado.
            $right->add([
                'profiles_id' => $profiles_id,
                'name'        => 'plugin_formaciones_formacion',
                'rights'      => ALLSTANDARDRIGHT
            ]);
        }
    }
}
