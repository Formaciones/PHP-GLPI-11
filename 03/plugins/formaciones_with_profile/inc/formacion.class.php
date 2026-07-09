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
    // Derecho propio del plugin usado para controlar acceso a Formaciones.
    public static $rightname = 'plugin_formaciones_formacion';

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

    /**ón la
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
        // Si el perfil activo no tiene permiso de lectura, no se muestra el menu.
        if (!self::canView()) {
            return [];
        }

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

        // Primera fila: nombre y estado.
        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Nombre', 'formaciones') . '</td>';
        echo '<td>';

        // Campo de texto para el nombre.
        echo Html::input('name', [
            'value' => $this->fields['name'] ?? '',
            'size'  => 60
        ]);
        echo '</td>';
        echo '<td>' . __('Estado', 'formaciones') . '</td>';
        echo '<td>';

        // Desplegable con los estados definidos en getStates().
        Dropdown::showFromArray('state', self::getStates(), [
            'value' => $this->fields['state'] ?? self::STATE_ACTIVE
        ]);
        echo '</td>';
        echo '</tr>';

        // Segunda fila: descripcion larga.
        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Descripcion', 'formaciones') . '</td>';
        echo '<td colspan="3">';

        // Area de texto para escribir la descripcion de la formacion.
        echo Html::textarea([
            'name'  => 'description',
            'value' => $this->fields['description'] ?? '',
            'cols'  => 100,
            'rows'  => 6
        ]);
        echo '</td>';
        echo '</tr>';

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
