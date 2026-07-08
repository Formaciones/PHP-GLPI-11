<?php

if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

class PluginFormacionesFormacion extends CommonDBTM
{

    public static $rightname = 'computer';

    public const STATE_INACTIVE = 0;
    public const STATE_ACTIVE = 1;

    public const FORMAT_ONLINE = 'online';
    public const FORMAT_PRESENTIAL = 'presencial';

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

        if (isset($input['name'])) {
            $input['name'] = trim($input['name']);
        }

        if (isset($input['description'])) {
            $input['description'] = trim($input['description']);
        }

        foreach ([
            'format',
            'trainer',
            'currency',
            'location',
            'meeting_url',
            'level',
            'target_audience',
            'observations'
        ] as $field) {
            if (isset($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }

        if (isset($input['state'])) {
            $input['state'] = (int) $input['state'];
        }

        foreach (['duration_hours', 'cost'] as $field) {
            if (isset($input[$field])) {
                $input[$field] = ($input[$field] === '')
                    ? 0
                    : (float) str_replace(',', '.', $input[$field]);
            }
        }

        if (isset($input['capacity'])) {
            $input['capacity'] = ($input['capacity'] === '') ? 0 : (int) $input['capacity'];
        }

        foreach (['start_date', 'end_date'] as $field) {
            if (isset($input[$field]) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        $input['certificate'] = isset($input['certificate']) ? 1 : 0;

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

    public static function getFormats()
    {
        return [
            self::FORMAT_ONLINE     => __('Online', 'formaciones'),
            self::FORMAT_PRESENTIAL => __('Presencial', 'formaciones')
        ];
    }

    public static function getTrainers()
    {
        return [
            'ana_garcia'      => 'Ana Garcia',
            'marcos_lopez'    => 'Marcos Lopez',
            'laura_sanchez'   => 'Laura Sanchez',
            'david_romero'    => 'David Romero',
            'nuria_martinez'  => 'Nuria Martinez'
        ];
    }

    public static function getLevels()
    {
        return [
            'inicial'    => __('Inicial', 'formaciones'),
            'intermedio' => __('Intermedio', 'formaciones'),
            'avanzado'   => __('Avanzado', 'formaciones')
        ];
    }

    public static function getCurrencies()
    {
        return [
            'EUR' => 'EUR',
            'USD' => 'USD',
            'GBP' => 'GBP'
        ];
    }

    public static function getStateName($state)
    {

        $states = self::getStates();

        return $states[(int) $state] ?? __('Desconocido', 'formaciones');
    }

    private static function getArrayValueName(array $values, $value)
    {
        return $values[$value] ?? (string) $value;
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
            'field'         => 'format',
            'name'          => __('Formato', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'specific',
            'searchtype'    => 'equals',
            'massiveaction' => false
        ];

        $tab[] = [
            'id'            => '8',
            'table'         => self::getTable(),
            'field'         => 'trainer',
            'name'          => __('Formador', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'specific',
            'searchtype'    => 'equals',
            'massiveaction' => false
        ];

        $tab[] = [
            'id'       => '9',
            'table'    => self::getTable(),
            'field'    => 'start_date',
            'name'     => __('Fecha de inicio', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'date'
        ];

        $tab[] = [
            'id'       => '10',
            'table'    => self::getTable(),
            'field'    => 'end_date',
            'name'     => __('Fecha de fin', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'date'
        ];

        $tab[] = [
            'id'       => '11',
            'table'    => self::getTable(),
            'field'    => 'duration_hours',
            'name'     => __('Horas', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'decimal'
        ];

        $tab[] = [
            'id'       => '12',
            'table'    => self::getTable(),
            'field'    => 'capacity',
            'name'     => __('Plazas', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'number'
        ];

        $tab[] = [
            'id'       => '13',
            'table'    => self::getTable(),
            'field'    => 'cost',
            'name'     => __('Coste', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'decimal'
        ];

        $tab[] = [
            'id'            => '14',
            'table'         => self::getTable(),
            'field'         => 'level',
            'name'          => __('Nivel', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'specific',
            'searchtype'    => 'equals',
            'massiveaction' => false
        ];

        $tab[] = [
            'id'       => '15',
            'table'    => self::getTable(),
            'field'    => 'location',
            'name'     => __('Ubicacion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'string'
        ];

        $tab[] = [
            'id'       => '16',
            'table'    => self::getTable(),
            'field'    => 'target_audience',
            'name'     => __('Destinatarios', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'string'
        ];

        $tab[] = [
            'id'            => '17',
            'table'         => self::getTable(),
            'field'         => 'certificate',
            'name'          => __('Certificado', 'formaciones'),
            'itemtype'      => self::class,
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

        if ($field === 'format') {
            return self::getArrayValueName(self::getFormats(), $values[$field] ?? '');
        }

        if ($field === 'trainer') {
            return self::getArrayValueName(self::getTrainers(), $values[$field] ?? '');
        }

        if ($field === 'level') {
            return self::getArrayValueName(self::getLevels(), $values[$field] ?? '');
        }

        if ($field === 'certificate') {
            return ((int) ($values[$field] ?? 0) === 1) ? __('Si') : __('No');
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

        if ($field === 'format') {
            return Dropdown::showFromArray($name, self::getFormats(), [
                'value'   => $values,
                'display' => false
            ]);
        }

        if ($field === 'trainer') {
            return Dropdown::showFromArray($name, self::getTrainers(), [
                'value'   => $values,
                'display' => false
            ]);
        }

        if ($field === 'level') {
            return Dropdown::showFromArray($name, self::getLevels(), [
                'value'   => $values,
                'display' => false
            ]);
        }

        if ($field === 'certificate') {
            return Dropdown::showFromArray($name, [
                0 => __('No'),
                1 => __('Si')
            ], [
                'value'   => $values,
                'display' => false
            ]);
        }

        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }
}
