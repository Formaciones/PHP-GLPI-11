<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

class PluginFormacionesProfile extends Profile
{
    public static $rightname = 'profile';

    public const RIGHT_FORMACION = 'plugin_formaciones_formacion';

    public static function install(Migration $migration)
    {
        global $DB;

        ProfileRight::addProfileRights([
            self::RIGHT_FORMACION
        ]);

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
        ProfileRight::deleteProfileRights([
            self::RIGHT_FORMACION
        ]);
    }

    public static function initProfile()
    {
        $profiles_id = (int) ($_SESSION['glpiactiveprofile']['id'] ?? 0);

        if ($profiles_id <= 0) {
            $_SESSION['glpiactiveprofile'][self::RIGHT_FORMACION] = 0;
            return;
        }

        $rights = ProfileRight::getProfileRights($profiles_id, [
            self::RIGHT_FORMACION
        ]);

        $_SESSION['glpiactiveprofile'][self::RIGHT_FORMACION]
            = (int) ($rights[self::RIGHT_FORMACION] ?? 0);
    }

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        return ($item instanceof Profile)
            ? __('Formaciones', 'formaciones')
            : '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if (!$item instanceof Profile) {
            return false;
        }

        $profile = new self();
        $profile->showForm((int) $item->getID());

        return true;
    }

    public function showForm($ID, array $options = [])
    {
        if (!Session::haveRight('profile', READ)) {
            return false;
        }

        $profiles_id = (int) $ID;
        $canedit = Session::haveRight('profile', UPDATE);

        $rights = ProfileRight::getProfileRights($profiles_id, [
            self::RIGHT_FORMACION
        ]);

        $current = (int) ($rights[self::RIGHT_FORMACION] ?? 0);

        $action = Plugin::getWebDir('formaciones') . '/front/profile.form.php';

        echo "<form method='post' action='" . htmlescape($action) . "'>";

        echo "<div class='center'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='2'>" . __('Permisos de Formaciones', 'formaciones') . "</th></tr>";

        self::showRightCheckbox(
            __('Leer', 'formaciones'),
            READ,
            $current,
            $canedit
        );

        self::showRightCheckbox(
            __('Crear', 'formaciones'),
            CREATE,
            $current,
            $canedit
        );

        self::showRightCheckbox(
            __('Modificar', 'formaciones'),
            UPDATE,
            $current,
            $canedit
        );

        self::showRightCheckbox(
            __('Eliminar', 'formaciones'),
            DELETE,
            $current,
            $canedit
        );

        self::showRightCheckbox(
            __('Eliminar permanentemente', 'formaciones'),
            PURGE,
            $current,
            $canedit
        );

        echo "</table>";
        echo "</div>";

        if ($canedit) {
            echo Html::hidden('profiles_id', ['value' => $profiles_id]);

            echo "<div class='center'>";
            echo Html::submit(__('Guardar'), [
                'name'  => 'update_rights',
                'class' => 'btn btn-primary'
            ]);
            echo "</div>";
        }

        Html::closeForm();

        return true;
    }

    private static function showRightCheckbox($label, $rightValue, $currentRights, $canedit)
    {
        $checked = (($currentRights & $rightValue) === $rightValue);

        echo "<tr class='tab_bg_1'>";
        echo "<td>" . htmlescape($label) . "</td>";
        echo "<td>";

        echo Html::hidden("rights_values[$rightValue]", ['value' => 0]);

        echo "<input type='checkbox'
                     name='rights_values[$rightValue]'
                     value='$rightValue' "
                     . ($checked ? "checked" : "") . " "
                     . (!$canedit ? "disabled" : "") .
             ">";

        echo "</td>";
        echo "</tr>";
    }

    public static function updateProfileRight($profiles_id, $rights)
    {
        $profileRight = new ProfileRight();

        if ($profileRight->getFromDBByCrit([
            'profiles_id' => $profiles_id,
            'name'        => self::RIGHT_FORMACION
        ])) {
            return $profileRight->update([
                'id'     => $profileRight->getID(),
                'rights' => $rights
            ]);
        }

        return $profileRight->add([
            'profiles_id' => $profiles_id,
            'name'        => self::RIGHT_FORMACION,
            'rights'      => $rights
        ]);
    }

    private static function addDefaultRights($profiles_id)
    {
        $right = new ProfileRight();

        if (!$right->getFromDBByCrit([
            'profiles_id' => $profiles_id,
            'name'        => self::RIGHT_FORMACION
        ])) {
            $right->add([
                'profiles_id' => $profiles_id,
                'name'        => self::RIGHT_FORMACION,
                'rights'      => ALLSTANDARDRIGHT
            ]);
        }
    }
}
