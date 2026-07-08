<?php


if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}


class PluginFormacionesFormacion extends CommonDBTM
{

    public static $rightname = 'plugin_formaciones_formacion';


    public const STATE_INACTIVE = 0;
    public const STATE_ACTIVE = 1;


    public static function getTypeName($nb = 0)
    {

        return _n('Formacion', 'Formaciones', $nb, 'formaciones');
    }


    public static function getMenuName()
    {
        return __('Formaciones', 'formaciones');
    }


    public static function getMenuContent()
    {

        if (!self::canView()) {
            return [];
        }


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
        return 'ti ti-school';
    }


    public static function getTable($classname = null)
    {
        return 'glpi_plugin_formaciones_formaciones';
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

        if (isset($input['name'])) {
            $input['name'] = trim($input['name']);
        }


        if (isset($input['description'])) {
            $input['description'] = trim($input['description']);
        }


        if (isset($input['state'])) {
            $input['state'] = (int) $input['state'];
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


        echo Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]);
        echo '</td>';
        echo '<td>' . __('Estado', 'formaciones') . '</td>';
        echo '<td>';


        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        echo '</td>';
        echo '</tr>';


        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Descripcion', 'formaciones') . '</td>';
        echo '<td colspan="3">';


        echo Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]);
        echo '</td>';
        echo '</tr>';


        $this->showFormButtons($options);


        return true;
    }


    public static function getStates()
    {
        return [
            self::STATE_ACTIVE   => __('Activo', 'formaciones'),
            self::STATE_INACTIVE => __('Inactivo', 'formaciones')
        ];
    }


    public static function getStateName($state)
    {

        $states = self::getStates();


        return $states[(int) $state] ?? __('Desconocido', 'formaciones');
    }


    public function rawSearchOptions()
    {

        $tab = parent::rawSearchOptions();


        $tab[] = [
            'id'            => '2',
            'table'         => self::getTable(),
            'field'         => 'name',
            'name'          => __('Nombre', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'itemlink',
            'massiveaction' => false
        ];


        $tab[] = [
            'id'       => '3',
            'table'    => self::getTable(),
            'field'    => 'description',
            'name'     => __('Descripcion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'text'
        ];


        $tab[] = [
            'id'            => '4',
            'table'         => self::getTable(),
            'field'         => 'state',
            'name'          => __('Estado', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'specific',
            'searchtype'    => 'equals',
            'massiveaction' => false
        ];


        $tab[] = [
            'id'       => '5',
            'table'    => self::getTable(),
            'field'    => 'date_creation',
            'name'     => __('Fecha de creacion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'datetime'
        ];


        $tab[] = [
            'id'       => '6',
            'table'    => self::getTable(),
            'field'    => 'date_mod',
            'name'     => __('Ultima modificacion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'datetime'
        ];


        return $tab;
    }


    public static function getSpecificValueToDisplay($field, $values, array $options = [])
    {

        if ($field === 'state') {
            return self::getStateName($values[$field] ?? self::STATE_INACTIVE);
        }


        return parent::getSpecificValueToDisplay($field, $values, $options);
    }


    public static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = [])
    {

        if ($field === 'state') {
            return Dropdown::showFromArray($name, self::getStates(), [
                'value'   => $values,
                'display' => false
            ]);
        }


        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }
}

