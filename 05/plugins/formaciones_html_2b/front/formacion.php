<?php

// Carga el entorno completo de GLPI: sesion, clases, DB, permisos, etc.
include('../../../inc/includes.php');

// Comprueba que el usuario tenga permiso de lectura para este objeto.
Session::checkRight(PluginFormacionesFormacion::$rightname, READ);

// Pinta la cabecera estandar de GLPI para esta pagina.
Html::header(
    PluginFormacionesFormacion::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesFormacion::class
);

// El listado estandar Search::show() dio problemas con SQLProvider en GLPI 11.
// Se deja comentado para que el alumno vea la alternativa nativa de GLPI.
// Search::show(PluginFormacionesFormacion::class);

// Objeto global de base de datos de GLPI.
global $DB;

// Tabla donde se guardan las formaciones.
$table = PluginFormacionesFormacion::getTable();

// Permiso para mostrar u ocultar el boton de anadir.
$can_create = PluginFormacionesFormacion::canCreate();

// Permiso para permitir enlazar cada fila al formulario de edicion.
$can_update = Session::haveRight(PluginFormacionesFormacion::$rightname, UPDATE);

// Contenedor centrado con estilos clasicos de GLPI.
echo "<div class='center'>";

// Si el usuario puede crear, se muestra el boton de alta.
if ($can_create) {
    echo "<p>";
    echo Html::link(
        __('Anadir', 'formaciones'),
        PluginFormacionesFormacion::getFormURLWithID(-1),
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
echo "<th>" . __('Formato', 'formaciones') . "</th>";
echo "<th>" . __('Formador', 'formaciones') . "</th>";
echo "<th>" . __('Fecha de inicio', 'formaciones') . "</th>";
echo "<th>" . __('Horas', 'formaciones') . "</th>";
echo "<th>" . __('Coste', 'formaciones') . "</th>";
echo "<th>" . __('Estado', 'formaciones') . "</th>";
echo "<th>" . __('Fecha de creacion', 'formaciones') . "</th>";
echo "<th>" . __('Ultima modificacion', 'formaciones') . "</th>";
echo "</tr>";

// Consulta de registros usando el query builder de GLPI.
$rows = $DB->request([
    'FROM'  => $table,
    'ORDER' => ['name', 'id']
]);

// Recorre cada fila devuelta por la base de datos.
foreach ($rows as $row) {
    // Convierte el ID a entero para trabajar de forma segura.
    $id = (int) $row['id'];

    // Si no hay nombre, muestra un texto basado en el ID.
    $name = $row['name'] !== '' ? $row['name'] : sprintf(__('ID %s'), $id);

    // Fila HTML del registro.
    echo "<tr class='tab_bg_1'>";
    echo "<td>" . htmlescape((string) $id) . "</td>";
    echo "<td>";

    // Si puede editar, el nombre enlaza al formulario.
    if ($can_update) {
        echo Html::link(
            htmlescape($name),
            PluginFormacionesFormacion::getFormURLWithID($id)
        );
    } else {
        // Si no puede editar, solo se muestra texto.
        echo htmlescape($name);
    }

    echo "</td>";

    // Muestra valores de catalogo con su etiqueta legible.
    echo "<td>" . htmlescape(PluginFormacionesFormacion::getSpecificValueToDisplay('format', $row)) . "</td>";
    echo "<td>" . htmlescape(PluginFormacionesFormacion::getSpecificValueToDisplay('trainer', $row)) . "</td>";
    echo "<td>" . htmlescape(Html::convDate($row['start_date'])) . "</td>";
    echo "<td>" . htmlescape((string) $row['duration_hours']) . "</td>";
    echo "<td>" . htmlescape((string) $row['cost'] . ' ' . ($row['currency'] ?? '')) . "</td>";

    // Convierte el estado numerico en texto legible.
    echo "<td>" . htmlescape(PluginFormacionesFormacion::getStateName($row['state'])) . "</td>";

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
