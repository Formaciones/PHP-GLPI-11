<?php
    $page = "api";
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | API Rest </title>
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
                    <h1 class="display-6 fw-bold mb-3">PHP API Rest</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="mx-auto" style="max-width: 1180px;">
                        <div class="text-center mb-5">
                            <!-- <span class="badge text-bg-primary mb-3">GLPI desde aplicaciones externas</span> -->
                            <h2 class="fw-bold mb-3">Consumo de API REST de GLPI</h2>
                            <p class="lead text-secondary mb-0">
                                Esta guía resume cómo activar la API, cómo autenticarse y cómo consumir
                                equipos de GLPI desde una aplicación externa usando HTTP.
                            </p>
                        </div>

                        <div class="row g-3 mt-4 text-start">
                            <div class="col-12 col-md-3">
                                <a href="device_list.php" class="btn btn-success w-100 py-3">
                                    Listado de Dispositivos
                                </a>
                            </div>
                            <div class="col-12 col-md-3">
                                <a href="tickets_list.php" class="btn btn-success w-100 py-3">
                                    Listado de Tickets
                                </a>
                            </div>
                            <div class="col-12 col-md-3">
                                <a href="formaciones_list.php" class="btn btn-success w-100 py-3">
                                    Listado de Formaciones
                                </a>
                            </div>

                            <div class="col-12 col-md-3">
                                <a href="tickets_create.php" class="btn btn-outline-success w-100 py-3">
                                    Crear Ticket
                                </a>                                
                            </div>
                            
                        </div>                        

                        <br />

                        <div class="alert alert-info border-0 shadow-sm mb-4">
                            <strong>Endpoint usado en los ejemplos:</strong>
                            <code>http://localhost:8080/apirest.php</code>.
                            Cambia la URL por la ruta real de tu instalación.
                        </div>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">1. API VS API Legacy</h3>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="border rounded-3 p-4 h-100">
                                        <h4 class="h5 fw-bold">API</h4>
                                        <p class="text-secondary mb-3">
                                            En versiones recientes de GLPI puede aparecer una sección llamada
                                            simplemente <strong>API</strong>. Se usa para funcionalidades modernas
                                            del producto y puede convivir con la API REST histórica.
                                        </p>
                                        <ul class="mb-0">
                                            <li>Puede variar según versión y módulos instalados.</li>
                                            <li>No todos los ejemplos clásicos de internet apuntan a esta API.</li>
                                            <li>Conviene revisar siempre la documentación de la versión instalada.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border rounded-3 p-4 h-100">
                                        <h4 class="h5 fw-bold">API Legacy / REST clásica</h4>
                                        <p class="text-secondary mb-3">
                                            Para integraciones sencillas y ejercicios formativos se usa normalmente
                                            la API REST clásica, expuesta por <code>apirest.php</code>.
                                        </p>
                                        <ul class="mb-0">
                                            <li>Permite leer, crear, actualizar y eliminar objetos de GLPI.</li>
                                            <li>Usa tipos de objeto como <code>Computer</code>, <code>Ticket</code>, <code>User</code>.</li>
                                            <li>Requiere sesión mediante <code>initSession</code>.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">2. Activación de la API REST</h3>
                            <ol class="list-group list-group-numbered shadow-sm">
                                <li class="list-group-item">
                                    Entrar en GLPI con un usuario administrador.
                                </li>
                                <li class="list-group-item">
                                    Ir a <strong>Configuración &gt; General &gt; API</strong>.
                                </li>
                                <li class="list-group-item">
                                    Activar la API REST clásica o <strong>API Legacy</strong>, según el nombre
                                    mostrado por tu versión.
                                </li>
                                <li class="list-group-item">
                                    Activar el método de autenticación que se vaya a usar:
                                    <strong>credenciales</strong> o <strong>token de usuario</strong>.
                                </li>
                                <li class="list-group-item">
                                    Crear un <strong>cliente API</strong> si se desea controlar el acceso por
                                    <code>App-Token</code>, rango IP o auditoría por aplicación.
                                </li>
                                <li class="list-group-item">
                                    Comprobar que el usuario usado por la aplicación externa tiene permisos
                                    sobre el objeto que se quiere consultar o crear.
                                </li>
                            </ol>
                        </section>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">3. Autenticación</h3>
                            <p class="text-secondary">
                                La API REST clásica trabaja en dos pasos: primero se abre sesión con
                                <code>initSession</code> y después se usa el <code>Session-Token</code>
                                devuelto en las llamadas siguientes.
                            </p>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="border rounded-3 p-4 h-100">
                                        <h4 class="h5 fw-bold">Con usuario y contraseña</h4>
                                        <p class="text-secondary">
                                            Se envía la cabecera <code>Authorization: Basic</code> con
                                            <code>base64(usuario:contraseña)</code>.
                                        </p>
<pre class="bg-dark text-light rounded-3 p-3 mb-0"><code>curl -X GET \
  -H "Content-Type: application/json" \
  -H "Authorization: Basic Z2xwaTpnbHBp" \
  -H "App-Token: TU_APP_TOKEN" \
  "http://localhost/glpi/apirest.php/initSession"</code></pre>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border rounded-3 p-4 h-100">
                                        <h4 class="h5 fw-bold">Con token de usuario</h4>
                                        <p class="text-secondary">
                                            El token se obtiene en las preferencias del usuario, si el acceso
                                            por token está habilitado.
                                        </p>
<pre class="bg-dark text-light rounded-3 p-3 mb-0"><code>curl -X GET \
  -H "Content-Type: application/json" \
  -H "Authorization: user_token TU_USER_TOKEN" \
  -H "App-Token: TU_APP_TOKEN" \
  "http://localhost/glpi/apirest.php/initSession"</code></pre>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="h5 fw-bold">Respuesta esperada</h4>
<pre class="bg-dark text-light rounded-3 p-3"><code>{
  "session_token": "83af7e620c83a50a18d3eac2f6ed05a3ca0bea62"
}</code></pre>
                                <p class="text-secondary mb-0">
                                    Ese valor se enviará después como cabecera <code>Session-Token</code>.
                                    Al terminar la integración conviene cerrar la sesión con
                                    <code>/killSession</code>.
                                </p>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">4. Recuperar listado de equipos</h3>
                            <p class="text-secondary">
                                En GLPI, los equipos se consultan con el tipo de objeto <code>Computer</code>.
                                La llamada devuelve registros paginados. Puedes controlar el rango con
                                <code>range=0-49</code> y pedir textos de desplegables con
                                <code>expand_dropdowns=true</code>.
                            </p>
<pre class="bg-dark text-light rounded-3 p-3"><code>curl -X GET \
  -H "Content-Type: application/json" \
  -H "Session-Token: TU_SESSION_TOKEN" \
  -H "App-Token: TU_APP_TOKEN" \
  "http://localhost/glpi/apirest.php/Computer/?range=0-49&amp;expand_dropdowns=true"</code></pre>

                            <h4 class="h5 fw-bold mt-4">Ejemplo en PHP</h4>
<pre class="bg-dark text-light rounded-3 p-3"><code>&lt;?php

$baseUrl = 'http://localhost/glpi/apirest.php';
$sessionToken = 'TU_SESSION_TOKEN';
$appToken = 'TU_APP_TOKEN';

$ch = curl_init($baseUrl . '/Computer/?range=0-49&amp;expand_dropdowns=true');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_HTTPHEADER =&gt; [
        'Content-Type: application/json',
        'Session-Token: ' . $sessionToken,
        'App-Token: ' . $appToken,
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 || $httpCode === 206) {
    $computers = json_decode($response, true);
    print_r($computers);
} else {
    echo 'Error consultando equipos: HTTP ' . $httpCode;
}</code></pre>
                        </section>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">5. Registrar un equipo</h3>
                            <p class="text-secondary">
                                Para crear un equipo se envía una petición <code>POST</code> a
                                <code>/Computer/</code>. El cuerpo JSON debe incluir una clave
                                <code>input</code> con los campos del equipo.
                            </p>
<pre class="bg-dark text-light rounded-3 p-3"><code>curl -X POST \
  -H "Content-Type: application/json" \
  -H "Session-Token: TU_SESSION_TOKEN" \
  -H "App-Token: TU_APP_TOKEN" \
  -d '{"input": {"name": "PC-AULA-01", "serial": "SN-2026-0001", "otherserial": "INV-0001"}}' \
  "http://localhost/glpi/apirest.php/Computer/"</code></pre>

                            <h4 class="h5 fw-bold mt-4">Ejemplo en PHP</h4>
<pre class="bg-dark text-light rounded-3 p-3"><code>&lt;?php

$baseUrl = 'http://localhost/glpi/apirest.php';
$sessionToken = 'TU_SESSION_TOKEN';
$appToken = 'TU_APP_TOKEN';

$payload = [
    'input' =&gt; [
        'name' =&gt; 'PC-AULA-01',
        'serial' =&gt; 'SN-2026-0001',
        'otherserial' =&gt; 'INV-0001',
        'comment' =&gt; 'Equipo creado desde una aplicacion externa',
    ],
];

$ch = curl_init($baseUrl . '/Computer/');
curl_setopt_array($ch, [
    CURLOPT_POST =&gt; true,
    CURLOPT_RETURNTRANSFER =&gt; true,
    CURLOPT_HTTPHEADER =&gt; [
        'Content-Type: application/json',
        'Session-Token: ' . $sessionToken,
        'App-Token: ' . $appToken,
    ],
    CURLOPT_POSTFIELDS =&gt; json_encode($payload),
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201) {
    $created = json_decode($response, true);
    echo 'Equipo creado con ID: ' . $created['id'];
} else {
    echo 'Error creando equipo: HTTP ' . $httpCode . PHP_EOL;
    echo $response;
}</code></pre>
                        </section>

                        <section class="mb-5">
                            <h3 class="h4 fw-bold mb-3">6. Cerrar sesión</h3>
                            <p class="text-secondary">
                                Después de terminar las llamadas, se recomienda destruir la sesión abierta.
                            </p>
<pre class="bg-dark text-light rounded-3 p-3"><code>curl -X GET \
  -H "Content-Type: application/json" \
  -H "Session-Token: TU_SESSION_TOKEN" \
  -H "App-Token: TU_APP_TOKEN" \
  "http://localhost/glpi/apirest.php/killSession"</code></pre>
                        </section>

                        <section>
                            <h3 class="h4 fw-bold mb-3">7. Errores habituales</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Causa frecuente</th>
                                            <th>Qué revisar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>401</code></td>
                                            <td>Autenticación incorrecta o token inválido.</td>
                                            <td>Usuario, contraseña, <code>User-Token</code>, <code>Session-Token</code> o permisos.</td>
                                        </tr>
                                        <tr>
                                            <td><code>400</code></td>
                                            <td>Petición mal formada.</td>
                                            <td>JSON, cabecera <code>Content-Type</code> y parámetros enviados.</td>
                                        </tr>
                                        <tr>
                                            <td><code>403</code></td>
                                            <td>Acceso bloqueado por configuración del cliente API.</td>
                                            <td>Rango IP, <code>App-Token</code> y estado del cliente API.</td>
                                        </tr>
                                        <tr>
                                            <td><code>206</code></td>
                                            <td>Respuesta parcial paginada.</td>
                                            <td>No es un error; usa <code>range</code> para recorrer más registros.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="small text-secondary mt-4">
                                Referencia usada:
                                <a href="https://raw.githubusercontent.com/glpi-project/glpi/main/apirest.md" target="_blank" rel="noopener">
                                    documentación oficial de la API REST clásica de GLPI
                                </a>.
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
