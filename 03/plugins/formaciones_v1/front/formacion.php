<?php


include('../../../inc/includes.php');


Session::checkRight(PluginFormacionesFormacion::$rightname, READ);


Html::header(
    PluginFormacionesFormacion::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesFormacion::class
);






global $DB;


$table = PluginFormacionesFormacion::getTable();


$can_create = PluginFormacionesFormacion::canCreate();


$can_update = Session::haveRight(PluginFormacionesFormacion::$rightname, UPDATE);


echo "<div class='center'>";


if ($can_create) {
    echo "<p>";
    echo Html::link(
        __('Anadir', 'formaciones'),
        PluginFormacionesFormacion::getFormURLWithID(-1),
        ['class' => 'btn btn-primary']
    );
    echo "</p>";
}


echo "<table class='tab_cadre_fixehov'>";


echo "<tr>";
echo "<th>" . __('ID') . "</th>";
echo "<th>" . __('Nombre', 'formaciones') . "</th>";
echo "<th>" . __('Descripcion', 'formaciones') . "</th>";
echo "<th>" . __('Estado', 'formaciones') . "</th>";
echo "<th>" . __('Fecha de creacion', 'formaciones') . "</th>";
echo "<th>" . __('Ultima modificacion', 'formaciones') . "</th>";
echo "</tr>";


$rows = $DB->request([
    'FROM'  => $table,
    'ORDER' => ['name', 'id']
]);


foreach ($rows as $row) {
    $id = (int) $row['id'];
    $name = $row['name'] !== '' ? $row['name'] : sprintf(__('ID %s'), $id);
    $description = $row['description'] ?? '';

    echo "<tr class='tab_bg_1'>";
    echo "<td>" . htmlescape((string) $id) . "</td>";
    echo "<td>";

    if ($can_update) {
        echo Html::link(
            htmlescape($name),
            PluginFormacionesFormacion::getFormURLWithID($id)
        );
    } else {

        echo htmlescape($name);
    }

    echo "</td>";

    echo "<td>" . nl2br(htmlescape($description)) . "</td>";
    echo "<td>" . htmlescape(PluginFormacionesFormacion::getStateName($row['state'])) . "</td>";
    echo "<td>" . htmlescape(Html::convDateTime($row['date_creation'])) . "</td>";
    echo "<td>" . htmlescape(Html::convDateTime($row['date_mod'])) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";


Html::footer();

