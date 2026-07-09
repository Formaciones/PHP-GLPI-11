<?php
    $page = "importexport";

    /**
     * Laboratorio 07: importar y consultar Formaciones usando la API REST de GLPI.
     *
     * Este ejemplo trabaja contra el itemtype creado por el plugin:
     * PluginFormacionesFormacion.
     */

    // Reutilizamos Guzzle instalado en labs/04 para no duplicar vendor.
    $autoload = __DIR__ . '/../04/vendor/autoload.php';
    $hasGuzzle = file_exists($autoload);

    if ($hasGuzzle) {
        require_once $autoload;
    }

    // Funcion auxiliar para escapar valores antes de pintarlos en HTML.
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    function formacion_status_name($state)
    {
        $states = [
            0 => 'Inactivo',
            1 => 'Activo',
        ];

        return $states[(int) $state] ?? (string) $state;
    }

    /**
     * Abre una sesion de API en GLPI, ejecuta una operacion y cierra la sesion.
     */
    function glpi_with_session($apiUrl, $appToken, $userToken, $login, $password, callable $callback)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => rtrim($apiUrl, '/') . '/',
            'timeout'  => 20,
            'verify'   => false,
        ]);

        $headers = [
            'Accept' => 'application/json',
        ];

        // Añade a la cabecera el token de Aplicación
        if ($appToken !== '') {
            $headers['App-Token'] = $appToken;
        }

        // Añade a la cabecera el token de Usuario o Usuario y Contraseña
        // GLPI permite iniciar sesion con user_token o con usuario/contrasena.
        if ($userToken !== '') {
            $headers['Authorization'] = 'user_token ' . $userToken;
        } elseif ($login !== '' || $password !== '') {
            $headers['Authorization'] = 'Basic ' . base64_encode($login . ':' . $password);
        }

        $sessionToken = '';

        // En la primera llamada al API obtenemos el token de sessión
        try {
            $sessionResponse = $client->get('initSession', [
                'headers' => $headers,
            ]);

            $sessionData = json_decode((string) $sessionResponse->getBody(), true);
            $sessionToken = $sessionData['session_token'] ?? '';

            if ($sessionToken === '') {
                throw new RuntimeException('GLPI no devolvio session_token.');
            }

            $headers['Session-Token'] = $sessionToken;
            unset($headers['Authorization']);

            return $callback($client, $headers);
        } finally {
            if ($sessionToken !== '') {
                try {
                    $client->get('killSession', [
                        'headers' => $headers,
                    ]);
                } catch (Throwable $ignored) {
                    // Si falla el cierre de sesion, no ocultamos el resultado principal.
                }
            }
        }
    }

    /**
     * Recupera formaciones desde GLPI y filtra por palabra contenida en descripcion.
     */
    function search_formaciones_by_description($client, array $headers, $word)
    {
        $response = $client->get('PluginFormacionesFormacion/', [
            'headers' => $headers,
            'query'   => [
                'range'            => '0-999',
                'expand_dropdowns' => 'true',
            ],
        ]);

        $rows = json_decode((string) $response->getBody(), true) ?: [];
        $word = trim((string) $word);

        if ($word === '') {
            return $rows;
        }

        return array_values(array_filter($rows, function ($row) use ($word) {
            return stripos((string) ($row['description'] ?? ''), $word) !== false;
        }));
    }

    /**
     * Crea una formacion usando el endpoint del itemtype del plugin.
     */
    function create_formacion($client, array $headers, $name, $description)
    {
        $headers['Content-Type'] = 'application/json';

        $response = $client->post('PluginFormacionesFormacion/', [
            'headers' => $headers,
            'json'    => [
                'input' => [
                    'name'        => $name,
                    'description' => $description,
                    'state'       => 1,
                ],
            ],
        ]);

        return json_decode((string) $response->getBody(), true) ?: [];
    }

    /**
     * Crea varias formaciones a partir de un array leido desde JSON.
     */
    function create_formaciones_from_json($client, array $headers, array $records)
    {
        $created = [];
        $omitted = 0;

        foreach ($records as $record) {
            if (!is_array($record)) {
                $omitted++;
                continue;
            }

            $name = trim((string) ($record['name'] ?? ''));
            $description = trim((string) ($record['description'] ?? ''));

            if ($name === '') {
                $omitted++;
                continue;
            }

            $created[] = create_formacion($client, $headers, $name, $description);
        }

        return [
            'created' => $created,
            'omitted' => $omitted,
        ];
    }

    // Valores por defecto desde variables de entorno o laboratorio local.
    $defaultApiUrl = getenv('GLPI_API_URL') ?: 'http://localhost:8080/apirest.php';
    $defaultAppToken = getenv('GLPI_APP_TOKEN') ?: '';
    $defaultUserToken = getenv('GLPI_USER_TOKEN') ?: '';
    $defaultLogin = getenv('GLPI_LOGIN') ?: '';
    $defaultPassword = getenv('GLPI_PASSWORD') ?: '';

    // Datos de conexion compartidos por los tres formularios.
    $apiUrl = trim($_POST['api_url'] ?? $defaultApiUrl);
    $appToken = trim($_POST['app_token'] ?? $defaultAppToken);
    $userToken = trim($_POST['user_token'] ?? $defaultUserToken);
    $login = trim($_POST['login'] ?? $defaultLogin);
    $password = (string) ($_POST['password'] ?? $defaultPassword);

    // Valores funcionales de los formularios.
    $descriptionWord = trim($_POST['description_word'] ?? '');
    $courseName = trim($_POST['course_name'] ?? '');
    $courseDescription = trim($_POST['course_description'] ?? '');

    $messages = [];
    $searchResults = [];
    $apiJsonOutput = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$hasGuzzle) {
            $messages[] = [
                'type' => 'warning',
                'text' => 'No se encuentra Guzzle. Comprueba que existe labs/04/vendor/autoload.php.'
            ];
        } elseif ($apiUrl === '') {
            $messages[] = [
                'type' => 'danger',
                'text' => 'Indica la URL de la API REST de GLPI.'
            ];
        } else {
            try {
                if (isset($_POST['search_formaciones'])) {
                    $searchResults = glpi_with_session(
                        $apiUrl,
                        $appToken,
                        $userToken,
                        $login,
                        $password,
                        function ($client, $headers) use ($descriptionWord) {
                            return search_formaciones_by_description($client, $headers, $descriptionWord);
                        }
                    );

                    $apiJsonOutput = json_encode($searchResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $messages[] = [
                        'type' => 'success',
                        'text' => 'Consulta completada. Resultados encontrados: ' . count($searchResults) . '.'
                    ];
                }

                if (isset($_POST['create_formacion'])) {
                    if ($courseName === '') {
                        throw new RuntimeException('El nombre del curso es obligatorio.');
                    }

                    $created = glpi_with_session(
                        $apiUrl,
                        $appToken,
                        $userToken,
                        $login,
                        $password,
                        function ($client, $headers) use ($courseName, $courseDescription) {
                            return create_formacion($client, $headers, $courseName, $courseDescription);
                        }
                    );

                    $apiJsonOutput = json_encode($created, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $messages[] = [
                        'type' => 'success',
                        'text' => 'Curso registrado mediante API de GLPI.'
                    ];
                }

                if (isset($_POST['import_json'])) {
                    $file = $_FILES['json_file'] ?? [];

                    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                        throw new RuntimeException('No se ha podido subir el fichero JSON.');
                    }

                    $json = file_get_contents($file['tmp_name']);
                    $records = json_decode($json, true);

                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($records)) {
                        throw new RuntimeException('El fichero no contiene un JSON valido.');
                    }

                    $result = glpi_with_session(
                        $apiUrl,
                        $appToken,
                        $userToken,
                        $login,
                        $password,
                        function ($client, $headers) use ($records) {
                            return create_formaciones_from_json($client, $headers, $records);
                        }
                    );

                    $apiJsonOutput = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $messages[] = [
                        'type' => 'success',
                        'text' => 'Importacion JSON completada. Cursos creados: '
                            . count($result['created'])
                            . '. Omitidos: '
                            . $result['omitted']
                            . '.'
                    ];
                }
            } catch (Throwable $exception) {
                $messages[] = [
                    'type' => 'danger',
                    'text' => 'Error usando la API de GLPI: ' . $exception->getMessage()
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
    <title>PHP + GLPI 11 | Import/Export Data</title>
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
                    <h1 class="display-6 fw-bold mb-3">Import/Export Data</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="mx-auto" style="max-width: 1180px;">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold mb-3">Formaciones mediante API REST de GLPI</h2>
                            <p class="lead text-secondary mb-0">
                                Consulta, registra e importa cursos usando el itemtype
                                <code>PluginFormacionesFormacion</code> creado por el plugin de Formaciones.
                            </p>
                        </div>

                        <?php if (!$hasGuzzle): ?>
                            <div class="alert alert-warning border-0 shadow-sm">
                                <strong>Guzzle no esta disponible.</strong>
                                Este laboratorio reutiliza <code>labs/04/vendor/autoload.php</code>.
                            </div>
                        <?php endif; ?>

                        <?php foreach ($messages as $message): ?>
                            <div class="alert alert-<?= e($message['type']) ?> border-0 shadow-sm">
                                <?= e($message['text']) ?>
                            </div>
                        <?php endforeach; ?>

                        <form method="post" enctype="multipart/form-data">
                            <section class="border rounded-3 p-4 mb-4">
                                <h3 class="h5 fw-bold mb-3">Configuracion de conexion</h3>

                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <label class="form-label" for="api_url">URL API REST</label>
                                        <input class="form-control" id="api_url" name="api_url" value="<?= e($apiUrl) ?>">
                                        <div class="form-text">Ejemplo: http://localhost:8080/apirest.php</div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label" for="app_token">App-Token</label>
                                        <input class="form-control" id="app_token" name="app_token" value="<?= e($appToken) ?>">
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label" for="user_token">User Token</label>
                                        <input class="form-control" id="user_token" name="user_token" value="<?= e($userToken) ?>">
                                        <div class="form-text">Si informas User Token, no hace falta usuario/contrasena.</div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="login">Usuario</label>
                                        <input class="form-control" id="login" name="login" value="<?= e($login) ?>">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label" for="password">Contrasena</label>
                                        <input class="form-control" id="password" name="password" type="password" value="<?= e($password) ?>">
                                    </div>
                                </div>
                            </section>

                            <div class="row g-4 mb-5">
                                <div class="col-lg-4">
                                    <section class="border rounded-3 p-4 h-100">
                                        <h3 class="h5 fw-bold mb-3">Consultar por descripcion</h3>
                                        <div class="mb-3">
                                            <label class="form-label" for="description_word">Palabra contenida</label>
                                            <input class="form-control" id="description_word" name="description_word" value="<?= e($descriptionWord) ?>">
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="search_formaciones" value="1">
                                            Consultar cursos
                                        </button>
                                    </section>
                                </div>

                                <div class="col-lg-4">
                                    <section class="border rounded-3 p-4 h-100">
                                        <h3 class="h5 fw-bold mb-3">Registrar curso</h3>
                                        <div class="mb-3">
                                            <label class="form-label" for="course_name">Nombre</label>
                                            <input class="form-control" id="course_name" name="course_name" value="<?= e($courseName) ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="course_description">Descripcion</label>
                                            <textarea class="form-control" id="course_description" name="course_description" rows="4"><?= e($courseDescription) ?></textarea>
                                        </div>
                                        <button class="btn btn-success" type="submit" name="create_formacion" value="1">
                                            Registrar curso
                                        </button>
                                    </section>
                                </div>

                                <div class="col-lg-4">
                                    <section class="border rounded-3 p-4 h-100">
                                        <h3 class="h5 fw-bold mb-3">Cargar JSON</h3>
                                        <div class="mb-3">
                                            <label class="form-label" for="json_file">Fichero JSON</label>
                                            <input class="form-control" id="json_file" name="json_file" type="file" accept=".json,application/json">
                                            <div class="form-text">Ejemplo disponible en <code>labs/07/datos.json</code>.</div>
                                        </div>
                                        <button class="btn btn-warning" type="submit" name="import_json" value="1">
                                            Cargar cursos
                                        </button>
                                    </section>
                                </div>
                            </div>
                        </form>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">JSON de respuesta</h3>
                            <textarea class="form-control font-monospace" rows="12" readonly><?= e($apiJsonOutput) ?></textarea>
                        </section>

                        <section>
                            <h3 class="h4 fw-bold mb-3">Resultados de consulta</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Descripcion</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($searchResults) === 0): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-secondary">
                                                    No hay resultados de consulta para mostrar.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($searchResults as $formacion): ?>
                                                <tr>
                                                    <td><?= e($formacion['id'] ?? '') ?></td>
                                                    <td><?= e($formacion['name'] ?? '') ?></td>
                                                    <td><?= e($formacion['description'] ?? '') ?></td>
                                                    <td><?= e(formacion_status_name($formacion['state'] ?? '')) ?></td>
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
