<?php
    $page = "api";

    /**
     * Ejemplo formativo: recuperar equipos de GLPI usando guzzlehttp/guzzle.
     *
     * Para instalar Guzzle en esta carpeta:
     *   cd C:\xampp\htdocs\labs\04
     *   composer require guzzlehttp/guzzle
     *
     * La pagina acepta configuracion por formulario y tambien por variables
     * de entorno para evitar escribir credenciales en el codigo.
     */

    // Ruta al autoload de Composer. Guzzle se carga desde vendor/autoload.php.
    $autoload = __DIR__ . '/vendor/autoload.php';

    // Comprobamos si las dependencias existen para mostrar un aviso amigable.
    $hasGuzzle = file_exists($autoload);

    // Solo cargamos Composer si esta instalado; asi la pagina no rompe al abrirla.
    if ($hasGuzzle) {
        require_once $autoload;
    }

    // Funcion auxiliar para escapar valores antes de pintarlos en HTML.
    // Evita que una respuesta de API o un valor de formulario inyecte HTML.
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    // Valores por defecto. Primero se leen variables de entorno y, si no existen,
    // se usan valores pensados para el laboratorio local.
    $defaultApiUrl = getenv('GLPI_API_URL') ?: 'http://localhost:8080/apirest.php';
    $defaultAppToken = getenv('GLPI_APP_TOKEN') ?: '';
    $defaultUserToken = getenv('GLPI_USER_TOKEN') ?: '';
    $defaultLogin = getenv('GLPI_LOGIN') ?: '';
    $defaultPassword = getenv('GLPI_PASSWORD') ?: '';

    // Valores enviados por el formulario. Si no hay POST, se usan los defaults.
    $apiUrl = trim($_POST['api_url'] ?? $defaultApiUrl);
    $appToken = trim($_POST['app_token'] ?? $defaultAppToken);
    $userToken = trim($_POST['user_token'] ?? $defaultUserToken);
    $login = trim($_POST['login'] ?? $defaultLogin);
    $password = (string) ($_POST['password'] ?? $defaultPassword);

    // Variables que alimentan la vista: JSON crudo, tabla y mensajes Bootstrap.
    $jsonOutput = '';
    $devices = [];
    $messages = [];

    // El flujo de API solo se ejecuta al pulsar el boton Recuperar equipos.
    if (isset($_POST['load_devices'])) {
        if (!$hasGuzzle) {
            $messages[] = [
                'type' => 'warning',
                'text' => 'No se encuentra vendor/autoload.php. Ejecuta: composer require guzzlehttp/guzzle en labs/04.'
            ];
        } elseif ($apiUrl === '') {
            $messages[] = [
                'type' => 'danger',
                'text' => 'Indica la URL de la API REST de GLPI.'
            ];
        } else {
            try {
                // Cliente HTTP de Guzzle. base_uri termina en / para poder usar
                // rutas relativas como initSession, Computer/ o killSession.
                $client = new \GuzzleHttp\Client([
                    'base_uri' => rtrim($apiUrl, '/') . '/',
                    'timeout'  => 15,
                    'verify'   => false,
                ]);

                // Cabeceras comunes. App-Token es opcional segun la configuracion
                // del cliente API en GLPI, pero en entornos reales suele usarse.
                $headers = [
                    'Accept' => 'application/json',
                ];

                if ($appToken !== '') {
                    $headers['App-Token'] = $appToken;
                }

                // GLPI permite iniciar sesion con user_token o con Basic auth.
                // Si hay user_token, lo priorizamos sobre usuario/contrasena.
                if ($userToken !== '') {
                    $headers['Authorization'] = 'user_token ' . $userToken;
                } elseif ($login !== '' || $password !== '') {
                    $headers['Authorization'] = 'Basic ' . base64_encode($login . ':' . $password);
                }

                // 1) Abrimos sesion en GLPI. La respuesta contiene session_token.
                $sessionResponse = $client->get('initSession', [
                    'headers' => $headers,
                ]);

                // Convertimos el JSON de respuesta a array PHP.
                $sessionData = json_decode((string) $sessionResponse->getBody(), true);
                $sessionToken = $sessionData['session_token'] ?? '';

                if ($sessionToken === '') {
                    throw new RuntimeException('GLPI no devolvio session_token.');
                }

                // 2) Las siguientes llamadas usan Session-Token.
                $headers['Session-Token'] = $sessionToken;

                // Authorization solo se necesita para initSession.
                unset($headers['Authorization']);

                // 3) Consultamos el itemtype nativo Computer para obtener equipos.
                $devicesResponse = $client->get('Computer/', [
                    'headers' => $headers,
                    'query'   => [
                        'range'            => '0-49',
                        'expand_dropdowns' => 'true',
                    ],
                ]);

                // Guardamos el JSON para mostrarlo en el textarea.
                $jsonOutput = (string) $devicesResponse->getBody();

                // Convertimos el JSON a array para pintar la tabla.
                $devices = json_decode($jsonOutput, true) ?: [];

                // Re-formateamos el JSON para que sea legible en pantalla.
                $jsonOutput = json_encode($devices, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                // 4) Cerramos la sesion abierta en GLPI.
                $client->get('killSession', [
                    'headers' => $headers,
                ]);

                $messages[] = [
                    'type' => 'success',
                    'text' => 'Listado de equipos recuperado correctamente.'
                ];
            } catch (Throwable $exception) {
                $messages[] = [
                    'type' => 'danger',
                    'text' => 'Error consultando GLPI: ' . $exception->getMessage()
                ];
            }
        }
    }
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | Listado de equipos con Guzzle</title>
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
                    <h1 class="display-6 fw-bold mb-3">Listado de equipos con Guzzle</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="mx-auto" style="max-width: 1180px;">
                        <div class="text-center mb-5">
                            <!-- <span class="badge text-bg-primary mb-3">GLPI API REST</span> -->
                            <h2 class="fw-bold mb-3">Recuperar dispositivos con guzzlehttp/guzzle</h2>
                            <p class="lead text-secondary mb-0">
                                Este ejemplo abre sesión en GLPI, consulta el endpoint <code>Computer</code>,
                                muestra el JSON recibido y después presenta una tabla simplificada.
                            </p>
                        </div>

                        <?php if (!$hasGuzzle): ?>
                            <div class="alert alert-warning border-0 shadow-sm">
                                <strong>Guzzle no está instalado en labs/04.</strong>
                                Ejecuta <code>composer require guzzlehttp/guzzle</code> dentro de
                                <code>C:\xampp\htdocs\labs\04</code>.
                            </div>
                        <?php endif; ?>

                        <?php foreach ($messages as $message): ?>
                            <div class="alert alert-<?= e($message['type']) ?> border-0 shadow-sm">
                                <?= e($message['text']) ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="row g-4 mb-5">
                            <div class="col-lg-5">
                                <div class="border rounded-3 p-4 h-100">
                                    <h3 class="h5 fw-bold mb-3">Configuración de conexión</h3>

                                    <form method="post">
                                        <div class="mb-3">
                                            <label class="form-label" for="api_url">URL API REST</label>
                                            <input class="form-control" id="api_url" name="api_url" value="<?= e($apiUrl) ?>">
                                            <div class="form-text">Ejemplo: http://localhost:8080/apirest.php</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="app_token">App-Token</label>
                                            <input class="form-control" id="app_token" name="app_token" value="<?= e($appToken) ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="user_token">User Token</label>
                                            <input class="form-control" id="user_token" name="user_token" value="<?= e($userToken) ?>">
                                            <div class="form-text">Si informas User Token, no hace falta usuario/contraseña.</div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="login">Usuario</label>
                                                <input class="form-control" id="login" name="login" value="<?= e($login) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="password">Contraseña</label>
                                                <input class="form-control" id="password" name="password" type="password" value="<?= e($password) ?>">
                                            </div>
                                        </div>

                                        <button class="btn btn-primary mt-4" type="submit" name="load_devices" value="1">
                                            Recuperar equipos
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="border rounded-3 p-4 h-100">
                                    <h3 class="h5 fw-bold mb-3">Código esencial con Guzzle</h3>
<pre class="bg-dark text-light rounded-3 p-3 mb-0"><code>$client = new \GuzzleHttp\Client([
    'base_uri' =&gt; 'http://localhost:8080/apirest.php/',
]);

$session = $client-&gt;get('initSession', [
    'headers' =&gt; [
        'App-Token' =&gt; 'TU_APP_TOKEN',
        'Authorization' =&gt; 'user_token TU_USER_TOKEN',
    ],
]);

$sessionToken = json_decode($session-&gt;getBody(), true)['session_token'];

$response = $client-&gt;get('Computer/', [
    'headers' =&gt; [
        'App-Token' =&gt; 'TU_APP_TOKEN',
        'Session-Token' =&gt; $sessionToken,
    ],
    'query' =&gt; [
        'range' =&gt; '0-49',
        'expand_dropdowns' =&gt; 'true',
    ],
]);</code></pre>
                                </div>
                            </div>
                        </div>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">JSON recibido</h3>
                            <textarea class="form-control font-monospace" rows="14" readonly><?= e($jsonOutput) ?></textarea>
                        </section>

                        <section>
                            <h3 class="h4 fw-bold mb-3">Tabla de equipos</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Serial</th>
                                            <th>Inventario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($devices) === 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary">
                                                    No hay datos para mostrar. Pulsa <strong>Recuperar equipos</strong>.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($devices as $device): ?>
                                                <tr>
                                                    <td><?= e($device['id'] ?? '') ?></td>
                                                    <td><?= e($device['name'] ?? '') ?></td>
                                                    <td><?= e($device['serial'] ?? '') ?></td>
                                                    <td><?= e($device['otherserial'] ?? '') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>

                    </div>
                </section>                

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
