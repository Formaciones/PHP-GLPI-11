<?php
    $page = "api";

    /**
     * Ejemplo formativo: crear un ticket de GLPI usando guzzlehttp/guzzle.
     *
     * Para instalar Guzzle en esta carpeta:
     *   cd C:\xampp\htdocs\labs\04
     *   composer install
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

    // Valores por defecto desde variables de entorno o laboratorio local.
    $defaultApiUrl = getenv('GLPI_API_URL') ?: 'http://localhost:8080/apirest.php';
    $defaultAppToken = getenv('GLPI_APP_TOKEN') ?: '';
    $defaultUserToken = getenv('GLPI_USER_TOKEN') ?: '';
    $defaultLogin = getenv('GLPI_LOGIN') ?: '';
    $defaultPassword = getenv('GLPI_PASSWORD') ?: '';

    // Datos de conexion enviados por formulario o cargados desde defaults.
    $apiUrl = trim($_POST['api_url'] ?? $defaultApiUrl);
    $appToken = trim($_POST['app_token'] ?? $defaultAppToken);
    $userToken = trim($_POST['user_token'] ?? $defaultUserToken);
    $login = trim($_POST['login'] ?? $defaultLogin);
    $password = (string) ($_POST['password'] ?? $defaultPassword);

    // Datos funcionales del ticket. Si la pagina se abre por GET, hay valores
    // de ejemplo para que el alumno pueda probar rapidamente.
    $ticketName = trim($_POST['ticket_name'] ?? 'Ticket creado desde API REST');
    $ticketContent = trim($_POST['ticket_content'] ?? 'Ejemplo formativo de creacion de ticket usando Guzzle.');
    $ticketUrgency = (int) ($_POST['urgency'] ?? 3);
    $ticketImpact = (int) ($_POST['impact'] ?? 3);
    $ticketPriority = (int) ($_POST['priority'] ?? 3);
    $ticketType = (int) ($_POST['type'] ?? 1);

    // Variables que alimentan la vista: JSON de respuesta, resumen y mensajes.
    $jsonOutput = '';
    $createdTicket = [];
    $messages = [];

    // El flujo de API solo se ejecuta al pulsar el boton Crear ticket.
    if (isset($_POST['create_ticket'])) {
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
        } elseif ($ticketName === '') {
            $messages[] = [
                'type' => 'danger',
                'text' => 'El titulo del ticket es obligatorio.'
            ];
        } else {
            try {
                // Cliente HTTP de Guzzle configurado con la URL base de apirest.php.
                $client = new \GuzzleHttp\Client([
                    'base_uri' => rtrim($apiUrl, '/') . '/',
                    'timeout'  => 15,
                    'verify'   => false,
                ]);

                // Cabeceras comunes. Content-Type es importante en POST JSON.
                $headers = [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
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

                // GLPI espera que el cuerpo de creacion vaya dentro de la clave input.
                $payload = [
                    'input' => [
                        'name'     => $ticketName,
                        'content'  => $ticketContent,
                        'urgency'  => $ticketUrgency,
                        'impact'   => $ticketImpact,
                        'priority' => $ticketPriority,
                        'type'     => $ticketType,
                    ],
                ];

                // 3) Creamos el ticket con POST sobre el itemtype Ticket.
                $ticketResponse = $client->post('Ticket/', [
                    'headers' => $headers,
                    'json'    => $payload,
                ]);

                // Guardamos el JSON para mostrarlo en el textarea.
                $jsonOutput = (string) $ticketResponse->getBody();

                // Convertimos el JSON a array para pintar el resumen.
                $createdTicket = json_decode($jsonOutput, true) ?: [];

                // Re-formateamos el JSON para que sea legible en pantalla.
                $jsonOutput = json_encode($createdTicket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                // 4) Cerramos la sesion abierta en GLPI.
                $client->get('killSession', [
                    'headers' => $headers,
                ]);

                $createdId = $createdTicket['id'] ?? '';
                $messages[] = [
                    'type' => 'success',
                    'text' => $createdId !== ''
                        ? 'Ticket creado correctamente con ID ' . $createdId . '.'
                        : 'Ticket creado correctamente.'
                ];
            } catch (Throwable $exception) {
                $messages[] = [
                    'type' => 'danger',
                    'text' => 'Error creando el ticket en GLPI: ' . $exception->getMessage()
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
    <title>PHP + GLPI 11 | Crear ticket con Guzzle</title>
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
                    <h1 class="display-6 fw-bold mb-3">Crear ticket con Guzzle</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="mx-auto" style="max-width: 1180px;">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-3">Crear un ticket mediante la API REST</h2>
                            <p class="lead text-secondary mb-0">
                                Este ejemplo abre sesion en GLPI y envia una peticion
                                <code>POST</code> al endpoint <code>Ticket/</code>.
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

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label" for="login">Usuario</label>
                                                <input class="form-control" id="login" name="login" value="<?= e($login) ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="password">Contrasena</label>
                                                <input class="form-control" id="password" name="password" type="password" value="<?= e($password) ?>">
                                            </div>
                                        </div>

                                        <h3 class="h5 fw-bold mb-3">Datos del ticket</h3>

                                        <div class="mb-3">
                                            <label class="form-label" for="ticket_name">Titulo</label>
                                            <input class="form-control" id="ticket_name" name="ticket_name" value="<?= e($ticketName) ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="ticket_content">Descripcion</label>
                                            <textarea class="form-control" id="ticket_content" name="ticket_content" rows="5"><?= e($ticketContent) ?></textarea>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="type">Tipo</label>
                                                <select class="form-select" id="type" name="type">
                                                    <option value="1" <?= $ticketType === 1 ? 'selected' : '' ?>>Incidencia</option>
                                                    <option value="2" <?= $ticketType === 2 ? 'selected' : '' ?>>Solicitud</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="priority">Prioridad</label>
                                                <select class="form-select" id="priority" name="priority">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <option value="<?= $i ?>" <?= $ticketPriority === $i ? 'selected' : '' ?>><?= $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="urgency">Urgencia</label>
                                                <select class="form-select" id="urgency" name="urgency">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <option value="<?= $i ?>" <?= $ticketUrgency === $i ? 'selected' : '' ?>><?= $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="impact">Impacto</label>
                                                <select class="form-select" id="impact" name="impact">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <option value="<?= $i ?>" <?= $ticketImpact === $i ? 'selected' : '' ?>><?= $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <button class="btn btn-primary mt-4" type="submit" name="create_ticket" value="1">
                                            Crear ticket
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

$response = $client-&gt;post('Ticket/', [
    'headers' =&gt; [
        'App-Token' =&gt; 'TU_APP_TOKEN',
        'Session-Token' =&gt; $sessionToken,
    ],
    'json' =&gt; [
        'input' =&gt; [
            'name' =&gt; 'Ticket creado desde API REST',
            'content' =&gt; 'Descripcion del ticket',
            'type' =&gt; 1,
            'urgency' =&gt; 3,
            'impact' =&gt; 3,
            'priority' =&gt; 3,
        ],
    ],
]);</code></pre>
                                </div>
                            </div>
                        </div>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">JSON de respuesta</h3>
                            <textarea class="form-control font-monospace" rows="12" readonly><?= e($jsonOutput) ?></textarea>
                        </section>

                        <section>
                            <h3 class="h4 fw-bold mb-3">Resumen del ticket creado</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Mensaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($createdTicket) === 0): ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-secondary">
                                                    Aun no se ha creado ningun ticket desde este formulario.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <tr>
                                                <td><?= e($createdTicket['id'] ?? '') ?></td>
                                                <td><?= e($createdTicket['message'] ?? 'Ticket creado') ?></td>
                                            </tr>
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
