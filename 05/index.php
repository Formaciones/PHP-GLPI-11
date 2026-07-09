<?php
    $page = "formularios";
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP + GLPI 11 | Formularios</title>
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
                    <h1 class="display-6 fw-bold mb-3">GLPI Formularios</h1>
                    <hr class="border-secondary-subtle opacity-100">
                </header>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm">
                    <div class="text-center mx-auto" style="max-width: 960px;">
                        <h2 class="h4 mb-3">Recursos GLPI para crear formularios</h2>

                        <p>En GLPI hay varias utilidades y convenciones que facilitan la creación de formularios en plugins o interfaces administrativas. A continuación encontrarás una descripción breve y ejemplos de uso.</p>

                        <ul class="text-start">
                            <li><strong>`$this->fields`</strong>: arreglo que define los campos del formulario (nombre, tipo, atributos, valores por defecto). Se usa para centralizar la definición y validación.</li>
                            <li><strong>`Html::input()`</strong>: genera un campo de entrada de texto.</li>
                            <li><strong>`Html::textarea()`</strong>: genera un área de texto multi-línea.</li>
                            <li><strong>`Dropdown::showFromArray()`</strong>: muestra un desplegable a partir de un array de opciones.</li>
                            <li><strong>`__()`</strong>: función de traducción; por ejemplo, <code>__('Texto', 'formaciones')</code> traduce la cadena en el dominio 'formaciones'.</li>
                            <li><strong>`self::getStates()`</strong>: ejemplo de método estático que puede devolver estados/valores usados por el formulario.</li>
                        </ul>

                        <h3 class="h6 mt-3">Ejemplo práctico (uso típico en un plugin)</h3>
                        <pre class="bg-light p-3 text-start" style="overflow:auto;">&lt;?php
// Definir campos (ejemplo simplificado)
$this->fields = [
    'title' => ['name' => 'title', 'type' => 'text', 'mandatory' => true],
    'description' => ['name' => 'description', 'type' => 'textarea']
];

// Renderizar input
echo Html::input('title', ['value' => $this->fields['title']['value'] ?? '']);

// Renderizar textarea
echo Html::textarea('description', ['value' => $this->fields['description']['value'] ?? '', 'rows' => 5]);

// Dropdown a partir de un array y uso de traducciones
$options = [
    'new' => __('Nuevo', 'formaciones'),
    'used' => __('Usado', 'formaciones')
];
Dropdown::showFromArray('state', $options, ['value' => (self::getStates()['state'] ?? 'new')] );
?&gt;</pre>

                        <p class="small text-muted">Consejo: centraliza la definición de los campos en <strong>`$this->fields`</strong> para reutilizar la lógica de validación y renderizado.</p>
                    </div>

                    <br />
                    
                    <div class="text-center mx-auto" style="max-width: 960px;">
                        <img class="img-fluid mx-auto d-block" src="../../img/5eb15a0a-53b7-40a7-bf04-5d24948eb76b.png" alt="Ejemplo de formulario en GLPI">
                    </div>
                </section>

                <section class="welcome-panel bg-white p-4 p-lg-5 shadow-sm mt-4">
                    <div class="text-center mx-auto" style="max-width: 960px;">
                        <div class="text-start">
                            <h3 class="h4 fw-semibold mb-4">Ejemplos de formularios desarrollados</h3>
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
                                            <td rowspan="5"><strong>formaciones</strong></td>
                                            <td>0.0.1</td>
                                            <td>Activo Formaciones con los campos Nombre, Estado y Descripción. El formulario se crea sin fichero de plantilla, se implementa dentro de la función ShowForm().</td>
                                        </tr>
                                        <tr>
                                            <td>5.0.1</td>
                                            <td>Activo Formaciones con los campos Nombre, Estado y Descripción. El formulario se crea mediante una plantilla, simplificando la función ShowForm().</td>
                                        </tr>
                                        <tr>
                                            <td>5.0.2</td>
                                            <td>Activo Formaciones. Este ejemplo muestra recursos para crear formularios mediante una plantilla. Los campos son de ejemplo no operativos.</td>
                                        </tr>
                                        <tr>
                                            <td>5.0.3</td>
                                            <td>Activo Formaciones con los campos Nombre, Estado, Computer y Descripción. El formulario se crea con diseño responsive y con un filtro para minimizar el espacio ocupado por el Dropdown.</td>
                                        </tr>
                                        <tr>
                                            <td>5.0.4</td>
                                            <td>Activo Formaciones con los campos Nombre, Estado, Tipo y Descripción. El formulario carga las opciones del tipo de formación consultando una API externa.</td>
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
