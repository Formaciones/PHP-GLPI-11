<?php
    $page = "hooks";
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | Hooks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/labs/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="layout d-flex">
        <?php
            include_once __DIR__ . '/../layout/aside.php';
        ?>

        <main class="flex-grow-1 p-4 p-lg-5">
            <div class="container-fluid">
                <header class="mb-4">
                    <h1 class="display-6 fw-bold mb-3">GLPI Hooks</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="text-start mx-auto" style="max-width: 960px;">
                        <h2 class="h4 fw-semibold mb-3">Estructura base del codigo del hook</h2>
                        <p class="mb-3">El plugin puede organizarse con esta estructura:</p>
                        <pre class="bg-dark text-light p-3 rounded"><code>plugins/
└── subcontractinstall/
    │
    ├── composer.json
    ├── setup.php
    ├── hook.php
    │
    ├── vendor/
    │
    └── inc/
        │
        ├── ApiClient.php
        ├── Config.php
        └── Logger.php</code></pre>

                        <h3 class="h5 mt-4">Paso 1. Crear el plugin</h3>
                        <pre class="bg-light border p-3 rounded"><code>plugins/
    subcontractinstall/</code></pre>

                        <h3 class="h5 mt-4">Paso 2. Crear composer.json</h3>
                        <pre class="bg-light border p-3 rounded"><code>{
    "name": "curso/subcontractinstall",
    "require": {
        "guzzlehttp/guzzle": "^7.9"
    },
    "autoload": {
        "psr-4": {
            "Plugin\\SubcontractInstall\\": "inc/"
        }
    }
}</code></pre>

                        <h3 class="h5 mt-4">Paso 3. Instalar dependencias</h3>
                        <p>Desde la carpeta del plugin:</p>
                        <pre class="bg-light border p-3 rounded"><code>composer install</code></pre>
                        <p class="mb-2">Obtendremos:</p>
                        <pre class="bg-light border p-3 rounded"><code>vendor/
vendor/autoload.php</code></pre>

                        <h3 class="h5 mt-4">Paso 4. setup.php</h3>
                        <pre class="bg-light border p-3 rounded"><code>&lt;?php

define('PLUGIN_SUBCONTRACTINSTALL_VERSION', '1.0.0');

function plugin_init_subcontractinstall()
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['subcontractinstall'] = true;

    require_once __DIR__ . '/vendor/autoload.php';
    require_once __DIR__ . '/hook.php';

    $PLUGIN_HOOKS['item_add']['subcontractinstall'] = [
        'Computer' =&gt; 'plugin_subcontractinstall_computer_added'
    ];
}

function plugin_version_subcontractinstall() {
    return [
        'name'           =&gt; 'Subcontract Install',
        'version'        =&gt; PLUGIN_SUBCONTRACTINSTALL_VERSION,
        'author'         =&gt; 'Borja',
        'license'        =&gt; 'GPL v2+',
        'homepage'       =&gt; '',
        'requirements'   =&gt; [
            'glpi' =&gt; [
                'min' =&gt; '11.0.0',
                'max' =&gt; '11.9.9'
            ]
        ]
    ];
}

function plugin_subcontractinstall_install()
{
    return true;
}

function plugin_subcontractinstall_uninstall()
{
    return true;
}

function plugin_subcontractinstall_check_prerequisites() 
{
    return true;
}

function plugin_subcontractinstall_check_config() 
{
    return true;
}</code></pre>
                        <p>Aqui ya se explica como integrar Composer dentro de un plugin.</p>

                        <h3 class="h5 mt-4">Paso 5. Hook</h3>
                        <pre class="bg-light border p-3 rounded"><code>&lt;?php

use Plugin\SubcontractInstall\ApiClient;

function plugin_subcontractinstall_computer_added(CommonDBTM $computer)
{
    ApiClient::sendNewComputer($computer);
}</code></pre>
                        <p class="mb-1">Muy limpio.</p>
                        <p>Toda la logica queda fuera.</p>

                        <h3 class="h5 mt-4">Paso 6. ApiClient.php</h3>
                        <pre class="bg-light border p-3 rounded"><code>&lt;?php

namespace Plugin\SubcontractInstall;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiClient
{
    public static function sendNewComputer(CommonDBTM $computer): void
    {
        $client = new Client([
            'base_uri' =&gt; 'https://empresa-demo.es/api/',
            'timeout' =&gt; 10
        ]);

        $payload = [
            'id' =&gt; $computer-&gt;fields['id'],
            'name' =&gt; $computer-&gt;fields['name'],
            'serial' =&gt; $computer-&gt;fields['serial'],
            'inventory' =&gt; $computer-&gt;fields['otherserial']
        ];

        try {
            $response = $client-&gt;post(
                'installations',
                [
                    'headers' =&gt; [
                        'Authorization' =&gt; 'Bearer 123456',
                        'Accept' =&gt; 'application/json'
                    ],
                    'json' =&gt; $payload
                ]
            );

            Toolbox::logInFile(
                "subcontractinstall",
                "Equipo enviado correctamente\n"
            );
        }
        catch (GuzzleException $e)
        {
            Toolbox::logInFile(
                "subcontractinstall",
                $e-&gt;getMessage()."\n"
            );
        }
    }
}</code></pre>

                        <h3 class="h5 mt-4">Paso 7. Copiar plugin al contenedor Docker y validar permisos</h3>
                        <p class="mb-2">Copiar el hook (plugin) a la carpeta de plugins dentro del contenedor:</p>
                        <pre class="bg-light border p-3 rounded"><code>docker cp "C:\xampp\htdocs\labs\03\plugins\subcontractinstall" glpi-web:/var/www/glpi/plugins/</code></pre>

                        <p class="mb-2">Cambiar propietario para que GLPI pueda leer correctamente el contenido del hook:</p>
                        <pre class="bg-light border p-3 rounded"><code>docker exec -it -u root glpi-web chown -R www-data:www-data /var/www/glpi/plugins/subcontractinstall</code></pre>

                        <p class="mb-2">Eliminar el plugin del contenedor si fuese necesario:</p>
                        <pre class="bg-light border p-3 rounded"><code>docker exec -u 0 -it glpi-web rm -rf /var/www/glpi/plugins/subcontractinstall</code></pre>

                        <p class="mb-2">Conectarse a la shell del contenedor para ejecutar comandos manualmente:</p>
                        <pre class="bg-light border p-3 rounded"><code>docker exec -it glpi-web bash</code></pre>

                        <p class="mb-2">Comandos utiles dentro del contenedor:</p>
                        <pre class="bg-light border p-3 rounded"><code>chown -R www-data:www-data /var/www/glpi/plugins/subcontractinstall

find /var/www/glpi/plugins/subcontractinstall -type d -exec chmod 755 {} \;

find /var/www/glpi/plugins/subcontractinstall -type f -exec chmod 644 {} \;

tail -100 /var/www/glpi/files/_log/php-errors.log

tail -100 /var/www/glpi/files/_log/php-errors.log | grep subcontract

tail -50 /var/www/glpi/files/_log/php-errors.log</code></pre>

                        <h3 class="h5 mt-4">Paso 8. Tabla de eventos comunes en hooks</h3>
                        <p class="mb-3">Estos son algunos eventos frecuentes para trabajar con objetos y extender comportamientos en GLPI:</p>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Hook</th>
                                        <th scope="col">Se ejecuta cuando...</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>item_add</code></td>
                                        <td>Se crea un objeto.</td>
                                    </tr>
                                    <tr>
                                        <td><code>item_update</code></td>
                                        <td>Se modifica un objeto existente.</td>
                                    </tr>
                                    <tr>
                                        <td><code>item_delete</code></td>
                                        <td>Se elimina un objeto (papelera o borrado logico).</td>
                                    </tr>
                                    <tr>
                                        <td><code>item_purge</code></td>
                                        <td>Se elimina definitivamente de la base de datos.</td>
                                    </tr>
                                    <tr>
                                        <td><code>pre_item_add</code></td>
                                        <td>Antes de insertar el objeto.</td>
                                    </tr>
                                    <tr>
                                        <td><code>pre_item_update</code></td>
                                        <td>Antes de actualizar un objeto.</td>
                                    </tr>
                                    <tr>
                                        <td><code>pre_item_delete</code></td>
                                        <td>Antes de eliminar un objeto.</td>
                                    </tr>
                                    <tr>
                                        <td><code>pre_item_purge</code></td>
                                        <td>Antes del borrado definitivo en base de datos.</td>
                                    </tr>
                                    <tr>
                                        <td><code>item_restore</code></td>
                                        <td>Cuando se restaura un objeto desde la papelera.</td>
                                    </tr>
                                    <tr>
                                        <td><code>post_init</code></td>
                                        <td>Despues de inicializar GLPI en cada peticion (carga global del plugin).</td>
                                    </tr>
                                    <tr>
                                        <td><code>pre_show_item</code></td>
                                        <td>Antes de renderizar la vista de un objeto (util para inyectar bloques en UI).</td>
                                    </tr>
                                    <tr>
                                        <td><code>csrf_compliant</code></td>
                                        <td>Declara que el plugin cumple validaciones CSRF de GLPI.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <br />

                        <div class="alert alert-info mb-0" role="alert">
                            Este enfoque se puede evolucionar con <strong>Hook + cola de trabajo</strong> para enviar la llamada a la API de forma asincrona y no bloquear la interfaz de GLPI.
                        </div>
                    </div>
                </section>                

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
