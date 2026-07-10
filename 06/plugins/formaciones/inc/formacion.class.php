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

        // Convierte el estado a entero para guardar 0 o 1.
        if (isset($input['state'])) {
            $input['state'] = (int) $input['state'];
        }

        // Convierte una fecha vacia en NULL para que la base de datos no reciba ''.
        foreach (['begin_date', 'end_date'] as $date_field) {
            if (array_key_exists($date_field, $input)) {
                $input[$date_field] = trim((string) $input[$date_field]);
                $input[$date_field] = $input[$date_field] === '' ? null : $input[$date_field];
            }
        }

        // Evita guardar plazas negativas aunque se manipule el formulario HTML.
        if (isset($input['number_places'])) {
            $input['number_places'] = max(0, (int) $input['number_places']);
        }

        // La validacion PHP replica la de JavaScript porque JS puede estar desactivado.
        if (
            !empty($input['begin_date'])
            && !empty($input['end_date'])
            && $input['begin_date'] > $input['end_date']
        ) {
            // GLPI mostrara el error y cancelara el alta o la actualizacion.
            Session::addMessageAfterRedirect(
                __('La fecha de inicio no puede ser posterior a la fecha de fin.', 'formaciones'),
                false,
                ERROR
            );
            return false;
        }

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

        // Columna Fecha de inicio, con los filtros de fecha nativos de GLPI.
        $tab[] = [
            'id'       => '7',
            'table'    => self::getTable(),
            'field'    => 'begin_date',
            'name'     => __('Fecha de inicio', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'date'
        ];

        // Columna Fecha de fin, con los filtros de fecha nativos de GLPI.
        $tab[] = [
            'id'       => '8',
            'table'    => self::getTable(),
            'field'    => 'end_date',
            'name'     => __('Fecha de fin', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'date'
        ];

        // Columna Numero de plazas, tratada como un entero por el buscador.
        $tab[] = [
            'id'       => '9',
            'table'    => self::getTable(),
            'field'    => 'number_places',
            'name'     => __('Numero de plazas', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'number'
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

        // Para otros campos usamos el selector estandar de GLPI.
        return parent::getSpecificValueToSelect($field, $name, $values, $options);
    }
}
