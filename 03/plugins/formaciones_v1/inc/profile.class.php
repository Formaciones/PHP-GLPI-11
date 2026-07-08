<?php


if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}


class PluginFormacionesProfile extends Profile
{
    public static $rightname = 'profile';

    public static function install(Migration $migration)
    {
        global $DB;

        ProfileRight::addProfileRights(['plugin_formaciones_formacion']);

        $profiles = $DB->request([
            'FROM' => Profile::getTable()
        ]);

        foreach ($profiles as $profile) {

            if (($profile['interface'] ?? '') !== 'helpdesk') {
                self::addDefaultRights((int) $profile['id']);
            }
        }
    }


    public static function uninstall(Migration $migration)
    {
        ProfileRight::deleteProfileRights(['plugin_formaciones_formacion']);
    }


    public static function initProfile()
    {
        foreach (['plugin_formaciones_formacion'] as $right) {

            if (!isset($_SESSION['glpiactiveprofile'][$right])) {
                $_SESSION['glpiactiveprofile'][$right] = 0;
            }
        }
    }


    private static function addDefaultRights($profiles_id)
    {
        $right = new ProfileRight();

        if (!$right->getFromDBByCrit([
            'profiles_id' => $profiles_id,
            'name'        => 'plugin_formaciones_formacion'
        ])) {

            $right->add([
                'profiles_id' => $profiles_id,
                'name'        => 'plugin_formaciones_formacion',
                'rights'      => ALLSTANDARDRIGHT
            ]);
        }
    }
}

