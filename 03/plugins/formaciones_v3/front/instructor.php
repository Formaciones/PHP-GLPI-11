<?php

// Carga el entorno completo de GLPI: sesion, clases, DB, permisos, etc.
include('../../../inc/includes.php');

// Comprueba que el usuario tenga permiso de lectura para este objeto.
Session::checkRight(PluginFormacionesInstructor::$rightname, READ);

Html::header(
    PluginFormacionesInstructor::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesInstructor::class
);

// Objeto global de base de datos de GLPI.
global $DB;

// Tabla donde se guardan los instructores.
$table = PluginFormacionesInstructor::getTable();

// Permiso para mostrar u ocultar el boton de anadir.
$can_create = PluginFormacionesInstructor::canCreate();

// Permiso para permitir enlazar cada fila al formulario de edicion.
$can_update = Session::haveRight(PluginFormacionesInstructor::$rightname, UPDATE);

// Contenedor centrado con estilos clasicos de GLPI.
echo "<div class='center'>";

// Si el usuario puede crear, se muestra el boton de alta.
if ($can_create) {
    echo "<p>";
    echo Html::link(
        __('Anadir', 'formaciones'),
        PluginFormacionesInstructor::getFormURLWithID(-1),
        ['class' => 'btn btn-primary']
    );
    echo "</p>";
}

// Inicio de la tabla de listado.
echo "<table class='tab_cadre_fixehov'>";

// Cabecera de columnas.
echo "<tr>";
echo "<th>" . __('ID') . "</th>";
echo "<th>" . __('Nombre', 'formaciones') . "</th>";
echo "<th>" . __('Apellidos', 'formaciones') . "</th>";
echo "<th>" . __('Numero de matricula', 'formaciones') . "</th>";
echo "<th>" . __('Fecha de creacion', 'formaciones') . "</th>";
echo "<th>" . __('Ultima modificacion', 'formaciones') . "</th>";
echo "</tr>";

// Consulta de registros usando el query builder de GLPI.
$rows = $DB->request([
    'FROM'  => $table,
    'ORDER' => ['name', 'firstname', 'id']
]);

// Recorre cada instructor devuelto por la base de datos.
foreach ($rows as $row) {
    // Convierte el ID a entero para trabajar de forma segura.
    $id = (int) $row['id'];

    // Usa cadena vacia si el nombre viene nulo.
    $firstname = $row['firstname'] ?? '';

    // Si no hay apellidos, muestra un texto basado en el ID.
    $name = $row['name'] !== '' ? $row['name'] : sprintf(__('ID %s'), $id);

    // Fila HTML del registro.
    echo "<tr class='tab_bg_1'>";
    echo "<td>" . htmlescape((string) $id) . "</td>";
    echo "<td>" . htmlescape($firstname) . "</td>";
    echo "<td>";

    // Si puede editar, los apellidos enlazan al formulario.
    if ($can_update) {
        echo Html::link(
            htmlescape($name),
            PluginFormacionesInstructor::getFormURLWithID($id)
        );
    } else {
        // Si no puede editar, solo se muestra texto.
        echo htmlescape($name);
    }

    echo "</td>";

    // Muestra el numero de matricula escapado para evitar HTML no deseado.
    echo "<td>" . htmlescape($row['registration_number'] ?? '') . "</td>";

    // Convierte fechas al formato configurado en GLPI.
    echo "<td>" . htmlescape(Html::convDateTime($row['date_creation'])) . "</td>";
    echo "<td>" . htmlescape(Html::convDateTime($row['date_mod'])) . "</td>";
    echo "</tr>";
}

// Cierre de tabla y contenedor.
echo "</table>";
echo "</div>";

// Pinta el pie estandar de GLPI.
Html::footer();
