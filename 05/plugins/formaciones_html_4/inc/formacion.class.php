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

    // API externa usada en el laboratorio para cargar tipos sin guardarlos en GLPI.
    private const TIPOS_API_URLS = [
        // URL habitual cuando GLPI se ejecuta directamente en XAMPP.
        'http://localhost/labs/05/api/tipos/',
        // URL util cuando GLPI se ejecuta dentro de un contenedor Docker.
        'http://host.docker.internal/labs/05/api/tipos/'
    ];

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
        // El tipo externo solo se muestra como ejemplo de consumo de API.
        // No existe una columna en la tabla, asi que se descarta antes de guardar.
        unset($input['_external_type']);

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
     * Obtiene los tipos desde un API externo.
     *
     * Este metodo es solo didactico: los datos se muestran en el formulario,
     * pero no se guardan en la tabla del plugin porque no hemos creado columna.
     */
    public static function getExternalTypes()
    {
        // Pedimos el JSON al API. Separamos esta parte para que sea facil de leer.
        $json = self::getExternalTypesJson();

        // Si el API no responde, devolvemos un array vacio y el formulario seguira cargando.
        if ($json === null || $json === '') {
            return [];
        }

        // Convierte el texto JSON en un array asociativo de PHP.
        $rows = json_decode($json, true);

        // Si el JSON no tiene la estructura esperada, evitamos romper la pantalla.
        if (!is_array($rows)) {
            return [];
        }

        $types = [];

        foreach ($rows as $row) {
            // Cada fila del API debe traer codigo y descripcion.
            if (!isset($row['codigo'], $row['descripcion'])) {
                continue;
            }

            $code = trim((string) $row['codigo']);
            $description = trim((string) $row['descripcion']);

            // Solo anadimos opciones completas al desplegable.
            if ($code !== '' && $description !== '') {
                $types[$code] = $description;
            }
        }

        return $types;
    }

    /**
     * Lee el JSON remoto usando cURL, con file_get_contents como alternativa sencilla.
     */
    private static function getExternalTypesJson()
    {
        foreach (self::TIPOS_API_URLS as $url) {
            $json = self::requestExternalTypesJson($url);

            if ($json !== null && $json !== '') {
                return $json;
            }
        }

        return null;
    }

    /**
     * Ejecuta una peticion HTTP al API de tipos.
     */
    private static function requestExternalTypesJson($url)
    {
        // cURL permite definir timeout para que el formulario no quede esperando mucho.
        if (function_exists('curl_init')) {
            $curl = curl_init($url);

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_HTTPHEADER     => ['Accept: application/json']
            ]);

            $response = curl_exec($curl);
            $httpcode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);

            // Solo aceptamos respuestas HTTP correctas.
            if ($response !== false && $httpcode >= 200 && $httpcode < 300) {
                return $response;
            }

            return null;
        }

        // Alternativa para entornos sin cURL. El @ evita avisos visibles en la pantalla.
        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 5,
                'header'  => "Accept: application/json\r\n"
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        return $response === false ? null : $response;
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
