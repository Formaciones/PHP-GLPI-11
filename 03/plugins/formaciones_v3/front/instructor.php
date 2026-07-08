<?php

include('../../../inc/includes.php');

Session::checkRight(PluginFormacionesInstructor::$rightname, READ);

Html::header(
    PluginFormacionesInstructor::getTypeName(Session::getPluralNumber()),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesInstructor::class
);

global $DB;

$table = PluginFormacionesInstructor::getTable();
$can_create = PluginFormacionesInstructor::canCreate();
$can_update = Session::haveRight(PluginFormacionesInstructor::$rightname, UPDATE);

echo "<div class='center'>";


if ($can_create) {
    echo "<p>";
    echo Html::link(
        __('Anadir', 'formaciones'),
        PluginFormacionesInstructor::getFormURLWithID(-1),
        ['class' => 'btn btn-primary']
    );
    echo "</p>";
}


echo "<table class='tab_cadre_fixehov'>";


echo "<tr>";
echo "<th>" . __('ID') . "</th>";
echo "<th>" . __('Nombre', 'formaciones') . "</th>";
echo "<th>" . __('Apellidos', 'formaciones') . "</th>";
echo "<th>" . __('Numero de matricula', 'formaciones') . "</th>";
echo "<th>" . __('Fecha de creacion', 'formaciones') . "</th>";
echo "<th>" . __('Ultima modificacion', 'formaciones') . "</th>";
echo "</tr>";


$rows = $DB->request([
    'FROM'  => $table,
    'ORDER' => ['name', 'firstname', 'id']
]);


foreach ($rows as $row) {
    $id = (int) $row['id'];
    $firstname = $row['firstname'] ?? '';
    $name = $row['name'] !== '' ? $row['name'] : sprintf(__('ID %s'), $id);

    echo "<tr class='tab_bg_1'>";
    echo "<td>" . htmlescape((string) $id) . "</td>";
    echo "<td>" . htmlescape($firstname) . "</td>";
    echo "<td>";

    if ($can_update) {
        echo Html::link(
            htmlescape($name),
            PluginFormacionesInstructor::getFormURLWithID($id)
        );
    } else {

        echo htmlescape($name);
    }

    echo "</td>";

    echo "<td>" . htmlescape($row['registration_number'] ?? '') . "</td>";

    echo "<td>" . htmlescape(Html::convDateTime($row['date_creation'])) . "</td>";
    echo "<td>" . htmlescape(Html::convDateTime($row['date_mod'])) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

Html::footer();

