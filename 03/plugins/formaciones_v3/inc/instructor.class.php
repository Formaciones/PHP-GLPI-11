<?php

// Seguridad basica: impide abrir este archivo directamente desde el navegador.
if (!defined('GLPI_ROOT')) {
    die('Sorry. You cannot access this file directly');
}

/**
 * Instructor asociado a una formacion.
 *
 * Esta clase representa un objeto propio del plugin. Al heredar de CommonDBTM,
 * GLPI sabe cargarlo desde base de datos, mostrar formularios, comprobar
 * permisos, guardar cambios y crear URLs estandar de listado/formulario.
 */
class PluginFormacionesInstructor extends CommonDBTM
{
    // Usamos el derecho "computer" para que quede protegido como otros activos.
    public static $rightname = 'computer';

    /**
     * Nombre singular/plural que GLPI mostrara en menus, cabeceras y migas.
     */
    public static function getTypeName($nb = 0)
    {
        return _n('Instructor', 'Instructores', $nb, 'formaciones');
    }

    /**
     * Texto visible en el menu Activos.
     */
    public static function getMenuName()
    {
        return __('Instructores', 'formaciones');
    }

    /**
     * Define como se pinta la entrada de menu del objeto Instructor.
     */
    public static function getMenuContent()
    {
        // GLPI espera un array con titulo, pagina principal, icono y enlaces.
        $menu = [];
        $menu['title'] = self::getMenuName();
        $menu['page'] = self::getSearchURL(false);
        $menu['icon'] = self::getIcon();
        $menu['links']['search'] = self::getSearchURL(false);

        // El enlace de alta solo se muestra si el perfil puede crear registros.
        if (self::canCreate()) {
            $menu['links']['add'] = self::getFormURL(false);
        }

        return $menu;
    }

    /**
     * Icono Tabler usado por GLPI en el menu.
     */
    public static function getIcon()
    {
        // "ti-user-school" no siempre esta disponible en el paquete de GLPI.
        // "ti-users" es una clase Tabler comun y evita que el icono quede vacio.
        return 'ti ti-users';
    }

    /**
     * Nombre fisico de la tabla de instructores.
     */
    public static function getTable($classname = null)
    {
        return 'glpi_plugin_formaciones_instructors';
    }

    /**
     * Pestañas visibles dentro del formulario de Instructor.
     */
    public function defineTabs($options = [])
    {
        // Pestaña principal con showForm().
        $tabs = [];
        $this->addDefaultFormTab($tabs);

        // Pestaña estandar de historial/logs del registro.
        $this->addStandardTab('Log', $tabs, $options);

        return $tabs;
    }

    /**
     * Limpia datos antes de insertar un instructor.
     */
    public function prepareInputForAdd($input)
    {
        return $this->cleanInput($input);
    }

    /**
     * Limpia datos antes de actualizar un instructor.
     */
    public function prepareInputForUpdate($input)
    {
        return $this->cleanInput($input);
    }

    /**
     * Normaliza campos recibidos desde el formulario.
     */
    private function cleanInput(array $input)
    {
        // Eliminamos espacios sobrantes para no guardar valores visualmente raros.
        foreach (['name', 'firstname', 'registration_number'] as $field) {
            if (isset($input[$field])) {
                $input[$field] = trim($input[$field]);
            }
        }

        return $input;
    }

    /**
     * Dibuja el formulario de alta/edicion del instructor.
     */
    public function showForm($ID, array $options = [])
    {
        // Inicializa el objeto para alta (-1) o carga el registro existente.
        $this->initForm($ID, $options);

        // Cabecera estandar con campos ocultos, token CSRF y acciones GLPI.
        $this->showFormHeader($options);

        // Primera fila: nombre y apellidos.
        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Nombre', 'formaciones') . '</td>';
        echo '<td>';

        // Campo Nombre. Internamente lo guardamos como firstname.
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

        // Segunda fila: numero de matricula.
        echo '<tr class="tab_bg_1">';
        echo '<td>' . __('Numero de matricula', 'formaciones') . '</td>';
        echo '<td colspan="3">';
        echo Html::input('registration_number', [
            'value' => $this->fields['registration_number'] ?? '',
            'size'  => 40
        ]);
        echo '</td>';
        echo '</tr>';

        // Botones estandar: guardar, borrar, restaurar, purgar, etc.
        $this->showFormButtons($options);

        return true;
    }

    /**
     * Declara las columnas que GLPI puede mostrar y filtrar en buscadores.
     */
    public function rawSearchOptions()
    {
        // Conserva columnas basicas heredadas de CommonDBTM.
        $tab = parent::rawSearchOptions();

        // Apellidos. Se usa itemlink para que enlace al formulario.
        $tab[] = [
            'id'            => '2',
            'table'         => self::getTable(),
            'field'         => 'name',
            'name'          => __('Apellidos', 'formaciones'),
            'itemtype'      => self::class,
            'datatype'      => 'itemlink',
            'massiveaction' => false
        ];

        // Nombre.
        $tab[] = [
            'id'       => '3',
            'table'    => self::getTable(),
            'field'    => 'firstname',
            'name'     => __('Nombre', 'formaciones'),
            'itemtype' => self::class,
            'datatype' => 'text'
        ];

        // Numero de matricula.
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

    /**
     * Nombre mostrado cuando otro objeto apunta a este instructor.
     */
    public function getName($options = [])
    {
        // Componemos "Nombre Apellidos" para que los desplegables sean legibles.
        $parts = array_filter([
            $this->fields['firstname'] ?? '',
            $this->fields['name'] ?? ''
        ]);
        $fullname = implode(' ', $parts);

        if ($fullname !== '') {
            return $fullname;
        }

        // Si no hay nombre ni apellidos, usamos el comportamiento base de GLPI.
        return parent::getName($options);
    }

    /**
     * Opciones para seleccionar instructores desde el formulario de Formacion.
     */
    public static function getDropdownOptions()
    {
        global $DB;

        $options = [];
        $iterator = $DB->request([
            'SELECT' => ['id', 'firstname', 'name', 'registration_number'],
            'FROM'   => self::getTable(),
            'ORDER'  => ['firstname', 'name']
        ]);

        foreach ($iterator as $row) {
            $label = trim(implode(' ', array_filter([
                $row['firstname'] ?? '',
                $row['name'] ?? ''
            ])));

            if ($label === '') {
                $label = sprintf(__('Instructor #%s', 'formaciones'), $row['id']);
            }

            if (!empty($row['registration_number'])) {
                $label .= ' - ' . $row['registration_number'];
            }

            $options[(int) $row['id']] = $label;
        }

        return $options;
    }
}
