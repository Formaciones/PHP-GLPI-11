<?php
    $page = "plugins";
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | Plugins</title>
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
                    <h1 class="display-6 fw-bold mb-3">GLPI Plugins</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="text-center mx-auto" style="max-width: 960px;">
                        <div class="text-start">
                            <h2 class="h4 fw-semibold mb-3">Marketplace y plugin TAG en GLPI 11</h2>
                            <p class="mb-4">
                                Desde la web de GLPI debemos ir a la opción:
                                <strong>Inicio -&gt; Configuración -&gt; General -&gt; GLPI Network</strong>
                                para registrarnos y poder acceder al Marketplace de plugins.
                            </p>

                            <div class="alert alert-info" role="alert">
                                Una vez instalado el plugin <strong>TAG</strong>, vamos a revisar dónde se almacena y su estructura en el contenedor Docker.
                            </div>

                            <h3 class="h5 mt-4 mb-3">Ver carpeta de plugins en el contenedor Docker</h3>
                            <p class="mb-2">Ejecuta estos comandos:</p>
                            <pre class="bg-dark text-light p-3 rounded"><code>docker exec -it glpi-web bash
ls -la /var/glpi/marketplace
find /var/glpi/marketplace -maxdepth 5 -type f</code></pre>

                            <h3 class="h5 mt-4 mb-3">Administrar etiquetas con TAG</h3>
                            <p class="mb-2">Tras la instalación de TAG, las etiquetas se administran desde el menú:</p>
                            <ul>
                                <li>Inicio</li>
                                <li>Configuración</li>
                                <li>Desplegables</li>
                                <li>Etiquetas</li>
                            </ul>
                            <p class="mb-0">
                                Acceso directo por URL:
                                <a href="http://localhost:8080/plugins/tag/front/tag.php" target="_blank" rel="noopener noreferrer">
                                    http://localhost:8080/plugins/tag/front/tag.php
                                </a>
                            </p>

                            <h3 class="h5 mt-5 mb-3">Ejemplos de plugins desarrollados</h3>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover border">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Plugin</th>
                                            <th scope="col">Versión</th>
                                            <th scope="col">Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>subcontractinstall</strong></td>
                                            <td>1.0.0</td>
                                            <td>Captura los eventos de creación o actualización de Computer y notifica a un microservicio externo.</td>
                                        </tr>                                        
                                        <tr>
                                            <td rowspan="5"><strong>formaciones</strong></td>
                                            <td>0.0.1</td>
                                            <td>Nuevo activo Formaciones con los campos Nombre, Estado y Descripción.</td>
                                        </tr>
                                        <tr>
                                            <td>1.0.0</td>
                                            <td>Nuevo activo Formaciones con los campos Nombre, Estado y Descripción. Define permisos pero no se utilizan.</td>
                                        </tr>
                                        <tr>
                                            <td>2.0.0</td>
                                            <td>Implementa la actualización de la versión 1.0.0 con nuevos campos.</td>
                                        </tr>
                                        <tr>
                                            <td>3.0.0</td>
                                            <td>Implementa la actualización de la versión 1.0.0 con nuevos campos relacionados como Instructor y tipo de formación.</td>
                                        </tr>
                                        <tr>
                                            <td>4.0.0</td>
                                            <td>Implementa la actualización de la versión 1.0.0. Define permisos que se utilizan. Es necesario asignar permisos al perfil para trabajar con formaciones.</td>
                                        </tr>
                                        <tr>
                                            <td><strong>extensiontickets</strong></td>
                                            <td>1.0.0</td>
                                            <td>Extiende la funcionalidad de gestión de tickets, añadiendo una pestaña con nueva información.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>                

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
