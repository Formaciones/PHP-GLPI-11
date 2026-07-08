<?php


if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}


class PluginFormacionesInstructor extends CommonDBTM
{

    public static $rightname = 'computer';


    public static function getTypeName($nb = 0)
    {
        return _n('Instructor', 'Instructores', $nb, 'formaciones');
    }


    public static function getMenuName()
    {
        return __('Instructores', 'formaciones');
    }


    public static function getMenuContent()
    {

        $menu = [];
        $menu['title'] = self::getMenuName();
        $menu['page'] = self::getSearchURL(false);
        $menu['icon'] = self::getIcon();
        $menu['links']['search'] = self::getSearchURL(false);


        if (self::canCreate()) {
            $menu['links']['add'] = self::getFormURL(false);
        }

        return $menu;
    }


    public static function getIcon()
    {


        return 'ti ti-users';
    }


    public static function getTable($classname = null)
    {
        return 'glpi_plugin_formaciones_instructors';
    }


    public function defineTabs($options = [])
    {

        $tabs = [];
        $this->addDefaultFormTab($tabs);


        $this->addStandardTab('Log', $tabs, $options);

        return $tabs;
    }


    public function prepareInputForAdd($input)
    {
        return $this->cleanInput($input);
    }


    public function prepareInputForUpdate($input)
    {
        return $this->cleanInput($input);
    }


    private function cleanInput(array $input)
    {

        foreach (['name', 'firstname', 'registration_number'] as $field) {
            if (isset($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }

        return $input;
    }


    public function showForm($ID, array $options = [])
    {

        $this->initForm($ID, $options);


        $this->showFormHeader($options);


        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Nombre', 'formaciones') . '</td>';
        echo '<td>';


        echo Html::input('firstname', [
            'value' => $this->fields['firstname'] ?? '',
            'size'  => 40
        ]);
        echo '</td>';
        echo '<td>' . __('Apellidos', 'formaciones') . '</td>';
        echo '<td>';
        echo Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 40
        ]);
        echo '</td>';
        echo '</tr>';


        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Numero de matricula', 'formaciones') . '</td>';
        echo '<td colspan="3">';
        echo Html::input('registration_number', [
            'value' => $this->fields['registration_number'] ?? '',
            'size'  => 40
        ]);
        echo '</td>';
        echo '</tr>';


        $this->showFormButtons($options);

        return true;
    }


    public function rawSearchOptions()
    {

        $tab = parent::rawSearchOptions();


        $tab[] = [
            'id'            => '2',
            'table'         => self::getTable(),
            'field'         => 'name',
            'name'          => __('Apellidos', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'itemlink',
            'massiveaction' => false
        ];


        $tab[] = [
            'id'       => '3',
            'table'    => self::getTable(),
            'field'    => 'firstname',
            'name'     => __('Nombre', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'text'
        ];


        $tab[] = [
            'id'       => '4',
            'table'    => self::getTable(),
            'field'    => 'registration_number',
            'name'     => __('Numero de matricula', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'text'
        ];

        return $tab;
    }


    public function getName($options = [])
    {

        $parts = array_filter([
            $this->fields['firstname'] ?? '',
            $this->fields['name'] ?? ''
        ]);
        $fullname = implode(' ', $parts);

        if ($fullname !== '') {
            return $fullname;
        }


        return parent::getName($options);
    }
}

