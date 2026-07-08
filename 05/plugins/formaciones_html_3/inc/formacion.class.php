<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

class PluginFormacionesFormacion extends CommonDBTM
{

    public static $rightname = 'computer';

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

        unset($input['computer_filter']);

        if (isset($input['name'])) {
            $input['name'] = trim($input['name']);
        }

        if (isset($input['description'])) {
            $input['description'] = trim($input['description']);
        }

        if (isset($input['state'])) {
            $input['state'] = (int) $input['state'];
        }

        if (isset($input['computers_id'])) {
            $input['computers_id'] = (int) $input['computers_id'];
        }

        return $input;
    }

    public function showForm($ID, array $options = [])
    {

        $this->initForm($ID, $options);

        $this->showFormHeader($options);

        include GLPI_ROOT . '/plugins/formaciones/templates/formacion_form.php';

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

    public static function getComputerName($computers_id)
    {
        $computers_id = (int) $computers_id;

        if ($computers_id <= 0) {
            return __('Ninguno', 'formaciones');
        }

        return Dropdown::getDropdownName(Computer::getTable(), $computers_id);
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
            'id'            => '7',
            'table'         => self::getTable(),
            'field'         => 'computers_id',
            'name'          => __('Equipo', 'formaciones'),
            'itemtype'      => Computer::class,
            'datatype'      => 'specific',
            'searchtype'    => 'equals',
            'massiveaction' => false
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

        if ($field === 'computers_id') {
            return self::getComputerName($values[$field] ?? 0);
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

        if ($field === 'computers_id') {
            return Dropdown::show(Computer::class, [
                'name'    => $name,
                'value'   => $values,
                'display' => false
            ]);
        }

        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }
}
