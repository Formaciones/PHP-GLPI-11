<?php
    $page = "api";

    /**
     * Ejemplo formativo: recuperar tickets de GLPI usando guzzlehttp/guzzle.
     *
     * Para instalar Guzzle en esta carpeta:
     *   cd C:\xampp\htdocs\labs\04
     *   composer install
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
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    function ticket_status_name($status)
    {
        // GLPI guarda el estado del ticket como numero. Esta tabla lo traduce.
        $statuses = [
            1 => 'Nuevo',
            2 => 'En curso asignado',
            3 => 'En curso planificado',
            4 => 'En espera',
            5 => 'Resuelto',
            6 => 'Cerrado',
        ];

        // Con expand_dropdowns algunos campos pueden venir como array.
        if (is_array($status)) {
            return $status['name'] ?? json_encode($status, JSON_UNESCAPED_UNICODE);
        }

        return $statuses[(int) $status] ?? (string) $status;
    }

    // Valores por defecto desde variables de entorno o laboratorio local.
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
    $tickets = [];
    $messages = [];

    // El flujo de API solo se ejecuta al pulsar el boton Recuperar tickets.
    if (isset($_POST['load_tickets'])) {
        if (!$hasGuzzle) {
            $messages[] = [
                'type' => 'warning',
                'text' => 'No se encuentra vendor/autoload.php. Ejecuta: composer install en labs/04.'
            ];
        } elseif ($apiUrl === '') {
            $messages[] = [
                'type' => 'danger',
                'text' => 'Indica la URL de la API REST de GLPI.'
            ];
        } else {
            try {
                // Cliente HTTP de Guzzle configurado con la URL base de apirest.php.
                $client = new \GuzzleHttp\Client([
                    'base_uri' => rtrim($apiUrl, '/') . '/',
                    'timeout'  => 15,
                    'verify'   => false,
                ]);

                // Cabeceras comunes para todas las llamadas a GLPI.
                $headers = [
                    'Accept' => 'application/json',
                ];

                if ($appToken !== '') {
                    $headers['App-Token'] = $appToken;
                }

                // GLPI permite iniciar sesion con user_token o con Basic auth.
                if ($userToken !== '') {
                    $headers['Authorization'] = 'user_token ' . $userToken;
                } elseif ($login !== '' || $password !== '') {
                    $headers['Authorization'] = 'Basic ' . base64_encode($login . ':' . $password);
                }

                // 1) Abrimos sesion y obtenemos session_token.
                $sessionResponse = $client->get('initSession', [
                    'headers' => $headers,
                ]);

                // Convertimos el JSON de respuesta a array PHP.
                $sessionData = json_decode((string) $sessionResponse->getBody(), true);
                $sessionToken = $sessionData['session_token'] ?? '';

                if ($sessionToken === '') {
                    throw new RuntimeException('GLPI no devolvio session_token.');
                }

                // 2) Usamos Session-Token en las llamadas siguientes.
                $headers['Session-Token'] = $sessionToken;

                // Authorization solo era necesario para initSession.
                unset($headers['Authorization']);

                // 3) Consultamos el itemtype Ticket.
                $ticketsResponse = $client->get('Ticket/', [
                    'headers' => $headers,
                    'query'   => [
                        'range'            => '0-49',
                        'expand_dropdowns' => 'true',
                    ],
                ]);

                // Guardamos el JSON para mostrarlo en el textarea.
                $jsonOutput = (string) $ticketsResponse->getBody();

                // Convertimos el JSON a array para pintar la tabla.
                $tickets = json_decode($jsonOutput, true) ?: [];

                // Re-formateamos el JSON para que sea legible en pantalla.
                $jsonOutput = json_encode($tickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                // 4) Cerramos la sesion abierta en GLPI.
                $client->get('killSession', [
                    'headers' => $headers,
                ]);

                $messages[] = [
                    'type' => 'success',
                    'text' => 'Listado de tickets recuperado correctamente.'
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
    <title>PHP + GLPI 11 | Listado de tickets con Guzzle</title>
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
                    <h1 class="display-6 fw-bold mb-3">Listado de tickets con Guzzle</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="mx-auto" style="max-width: 1180px;">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-3">Recuperar tickets con guzzlehttp/guzzle</h2>
                            <p class="lead text-secondary mb-0">
                                Este ejemplo abre sesion en GLPI, consulta el endpoint <code>Ticket</code>,
                                muestra el JSON recibido y despues presenta una tabla simplificada.
                            </p>
                        </div>

                        <?php if (!$hasGuzzle): ?>
                            <div class="alert alert-warning border-0 shadow-sm">
                                <strong>Guzzle no esta instalado en labs/04.</strong>
                                Ejecuta <code>composer install</code> dentro de
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
                                    <h3 class="h5 fw-bold mb-3">Configuracion de conexion</h3>

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
                                            <div class="form-text">Si informas User Token, no hace falta usuario/contrasena.</div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="login">Usuario</label>
                                                <input class="form-control" id="login" name="login" value="<?= e($login) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="password">Contrasena</label>
                                                <input class="form-control" id="password" name="password" type="password" value="<?= e($password) ?>">
                                            </div>
                                        </div>

                                        <button class="btn btn-primary mt-4" type="submit" name="load_tickets" value="1">
                                            Recuperar tickets
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="border rounded-3 p-4 h-100">
                                    <h3 class="h5 fw-bold mb-3">Codigo esencial con Guzzle</h3>
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

$response = $client-&gt;get('Ticket/', [
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
                            <h3 class="h4 fw-bold mb-3">Tabla de tickets</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Titulo</th>
                                            <th>Estado</th>
                                            <th>Fecha apertura</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($tickets) === 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary">
                                                    No hay datos para mostrar. Pulsa <strong>Recuperar tickets</strong>.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($tickets as $ticket): ?>
                                                <tr>
                                                    <td><?= e($ticket['id'] ?? '') ?></td>
                                                    <td><?= e($ticket['name'] ?? '') ?></td>
                                                    <td><?= e(ticket_status_name($ticket['status'] ?? '')) ?></td>
                                                    <td><?= e($ticket['date'] ?? '') ?></td>
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
