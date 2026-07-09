<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Clase principal del objeto "Formacion".
 *
 * Al extender CommonDBTM, GLPI le da soporte para alta, edicion, borrado,
 * permisos, formularios y gestion basica de registros en base de datos.
 */
class PluginFormacionesFormacion extends CommonDBTM
{
    // Derecho de GLPI usado para controlar acceso. "computer" pertenece a Activos.
    public static $rightname = 'computer';

    // Estados posibles de una formacion. Usamos constantes para evitar numeros magicos.
    public const STATE_INACTIVE = 0;
    public const STATE_ACTIVE = 1;

    // Formatos posibles para impartir la formacion.
    public const FORMAT_ONLINE = 'online';
    public const FORMAT_PRESENTIAL = 'presencial';

    /**
     * Nombre singular/plural que GLPI muestra para este tipo de objeto.
     */
    public static function getTypeName($nb = 0)
    {
        // _n permite traducir singular/plural segun el numero recibido.
        return _n('Formacion', 'Formaciones', $nb, 'formaciones');
    }

    /**
     * Nombre visible en el menu.
     */
    public static function getMenuName()
    {
        return __('Formaciones', 'formaciones');
    }

    /**
     * Define como aparece el plugin dentro del menu de GLPI.
     */
    public static function getMenuContent()
    {
        // Estructura esperada por GLPI para pintar una entrada de menu.
        $menu = [];

        // Texto del menu.
        $menu['title'] = self::getMenuName();

        // Pagina principal del listado.
        $menu['page'] = self::getSearchURL(false);

        // Icono Tabler usado por GLPI.
        $menu['icon'] = self::getIcon();

        // Enlace de busqueda/listado.
        $menu['links']['search'] = self::getSearchURL(false);

        // Solo muestra el enlace de alta si el perfil puede crear registros.
        if (self::canCreate()) {
            $menu['links']['add'] = self::getFormURL(false);
        }

        // Devuelve la configuracion completa del menu.
        return $menu;
    }

    /**
     * Icono usado por GLPI para el menu.
     */
    public static function getIcon()
    {
        return 'ti ti-school';
    }

    /**
     * Nombre fisico de la tabla que almacena las formaciones.
     */
    public static function getTable($classname = null)
    {
        return 'glpi_plugin_formaciones_formaciones';
    }

    /**
     * Define las pestanas visibles dentro del formulario del objeto.
     */
    public function defineTabs($options = [])
    {
        // Array donde GLPI espera recibir las pestanas.
        $tabs = [];

        // Pestana principal con el formulario definido en showForm().
        $this->addDefaultFormTab($tabs);

        // Pestana estandar de historial/logs del registro.
        $this->addStandardTab('Log', $tabs, $options);

        // Devuelve las pestanas al motor de GLPI.
        return $tabs;
    }

    /**
     * Limpia datos antes de insertar una nueva formacion.
     */
    public function prepareInputForAdd($input)
    {
        return $this->cleanInput($input);
    }

    /**
     * Limpia datos antes de actualizar una formacion existente.
     */
    public function prepareInputForUpdate($input)
    {
        return $this->cleanInput($input);
    }

    /**
     * Normaliza los datos recibidos desde el formulario.
     */
    private function cleanInput(array $input)
    {
        // Quita espacios al principio y al final del nombre.
        if (isset($input['name'])) {
            $input['name'] = trim($input['name']);
        }

        // Quita espacios al principio y al final de la descripcion.
        if (isset($input['description'])) {
            $input['description'] = trim($input['description']);
        }

        // Campos de texto simples del formulario de demostracion.
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

        // Convierte el estado a entero para guardar 0 o 1.
        if (isset($input['state'])) {
            $input['state'] = (int) $input['state'];
        }

        // Normaliza campos numericos para guardar valores consistentes.
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

        // Las fechas vacias se guardan como NULL para ser compatibles con MySQL.
        foreach (['start_date', 'end_date'] as $field) {
            if (isset($input[$field]) && $input[$field] === '') {
                $input[$field] = null;
            }
        }

        // Checkbox: si no viene en POST, se considera desmarcado.
        $input['certificate'] = isset($input['certificate']) ? 1 : 0;

        // Devuelve el array ya saneado a GLPI.
        return $input;
    }

    /**
     * Pinta el formulario de alta/edicion de una formacion.
     */
    public function showForm($ID, array $options = [])
    {
        // Inicializa campos internos: nuevo registro o registro existente.
        $this->initForm($ID, $options);

        // Pinta la cabecera estandar del formulario de GLPI.
        $this->showFormHeader($options);

        include GLPI_ROOT . '/plugins/formaciones/templates/formacion_form.php';

        // Pinta los botones estandar: guardar, borrar, restaurar, etc.
        $this->showFormButtons($options);

        // true indica que el formulario se mostro correctamente.
        return true;
    }

    /**
     * Lista de estados disponibles para una formacion.
     */
    public static function getStates()
    {
        return [
            self::STATE_ACTIVE   => __('Activo', 'formaciones'),
            self::STATE_INACTIVE => __('Inactivo', 'formaciones')
        ];
    }

    /**
     * Lista de formatos disponibles.
     */
    public static function getFormats()
    {
        return [
            self::FORMAT_ONLINE     => __('Online', 'formaciones'),
            self::FORMAT_PRESENTIAL => __('Presencial', 'formaciones')
        ];
    }

    /**
     * Lista ficticia de formadores para la demo.
     */
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

    /**
     * Niveles posibles de una formacion.
     */
    public static function getLevels()
    {
        return [
            'inicial'    => __('Inicial', 'formaciones'),
            'intermedio' => __('Intermedio', 'formaciones'),
            'avanzado'   => __('Avanzado', 'formaciones')
        ];
    }

    /**
     * Monedas disponibles para los costes.
     */
    public static function getCurrencies()
    {
        return [
            'EUR' => 'EUR',
            'USD' => 'USD',
            'GBP' => 'GBP'
        ];
    }

    /**
     * Devuelve el texto asociado a un estado numerico.
     */
    public static function getStateName($state)
    {
        // Recupera el array de estados posibles.
        $states = self::getStates();

        // Si el estado no existe, muestra un valor generico.
        return $states[(int) $state] ?? __('Desconocido', 'formaciones');
    }

    /**
     * Devuelve el texto asociado a un valor de catalogo.
     */
    private static function getArrayValueName(array $values, $value)
    {
        return $values[$value] ?? (string) $value;
    }

    /**
     * Define columnas disponibles para el buscador/listado estandar de GLPI.
     */
    public function rawSearchOptions()
    {
        // Empieza con las opciones heredadas de CommonDBTM.
        $tab = parent::rawSearchOptions();

        // Columna Nombre. itemlink permite enlazar al formulario del registro.
        $tab[] = [
            'id'            => '2',
            'table'         => self::getTable(),
            'field'         => 'name',
            'name'          => __('Nombre', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'itemlink',
            'massiveaction' => false
        ];

        // Columna Descripcion. Se trata como texto simple.
        $tab[] = [
            'id'       => '3',
            'table'    => self::getTable(),
            'field'    => 'description',
            'name'     => __('Descripcion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'text'
        ];

        // Columna Formato. Permite filtrar entre online y presencial.
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

        // Columna Formador. Lista ficticia para demostrar desplegables.
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

        // Columna Estado. datatype specific permite personalizar su visualizacion.
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

        // Columna Fecha de creacion.
        $tab[] = [
            'id'       => '5',
            'table'    => self::getTable(),
            'field'    => 'date_creation',
            'name'     => __('Fecha de creacion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'datetime'
        ];

        // Columna Ultima modificacion.
        $tab[] = [
            'id'       => '6',
            'table'    => self::getTable(),
            'field'    => 'date_mod',
            'name'     => __('Ultima modificacion', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'datetime'
        ];

        // Devuelve todas las columnas al buscador de GLPI.
        return $tab;
    }

    /**
     * Convierte valores especificos antes de mostrarlos en el listado.
     */
    public static function getSpecificValueToDisplay($field, $values, array $options = [])
    {
        // Para el campo state mostramos Activo/Inactivo en vez de 1/0.
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

        // Para otros campos usamos el comportamiento estandar de GLPI.
        return parent::getSpecificValueToDisplay($field, $values, $options);
    }

    /**
     * Pinta controles de busqueda especificos para campos especiales.
     */
    public static function getSpecificValueToSelect($field, $name = '', $values = '', array $options = [])
    {
        // Para state, el filtro de busqueda es un desplegable de estados.
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

        // Para otros campos usamos el selector estandar de GLPI.
        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }
}
