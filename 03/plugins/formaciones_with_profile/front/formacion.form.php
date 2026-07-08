<?php


include('../../../inc/includes.php');


$formacion = new PluginFormacionesFormacion();


if (isset($_POST['add'])) {

    $formacion->check(-1, CREATE, $_POST);


    $new_id = $formacion->add($_POST);


    Html::redirect(PluginFormacionesFormacion::getFormURLWithID($new_id));
}


if (isset($_POST['update'])) {

    $formacion->check((int) $_POST['id'], UPDATE);


    $formacion->update($_POST);


    Html::back();
}


if (isset($_POST['delete'])) {

    $formacion->check((int) $_POST['id'], DELETE);


    $formacion->delete($_POST);


    $formacion->redirectToList();
}


if (isset($_POST['purge'])) {

    $formacion->check((int) $_POST['id'], PURGE);


    $formacion->delete($_POST, 1);


    $formacion->redirectToList();
}


if (isset($_POST['restore'])) {

    $formacion->check((int) $_POST['id'], PURGE);


    $formacion->restore($_POST);


    Html::back();
}


$id = $_GET['id'] ?? -1;


if ($id > 0) {

    $formacion->check((int) $id, READ);
} else {

    $formacion->check(-1, CREATE);
}


Html::header(
    PluginFormacionesFormacion::getTypeName(1),
    $_SERVER['PHP_SELF'],
    'assets',
    PluginFormacionesFormacion::class
);


$formacion->display([
    'id' => $id
]);


Html::footer();

